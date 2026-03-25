<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as postsIndex } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import Input from '@/components/ui/input/Input.vue';
import { Textarea } from '@/components/ui/textarea';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

type Marker = {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    description: string | null;
    added: string | null;
    edited: string | null;
};

type WeatherPayload = {
    main: {
        temp: number;
    };
    weather: Array<{
        description: string;
        icon: string;
    }>;
} | null;

type LeafletMap = {
    setView: (center: [number, number], zoom: number) => void;
    fitBounds: (bounds: unknown, options?: { padding?: [number, number] }) => void;
    on: (event: string, callback: (event: { latlng: { lat: number; lng: number } }) => void) => void;
    off: () => void;
    remove: () => void;
};

type LeafletMarker = {
    addTo: (target: unknown) => LeafletMarker;
    bindPopup: (content: string) => LeafletMarker;
    on: (event: string, callback: () => void) => LeafletMarker;
    openPopup: () => void;
};

type LeafletLayerGroup = {
    clearLayers: () => void;
    addTo: (target: unknown) => LeafletLayerGroup;
};

type LeafletStatic = {
    map: (element: HTMLElement) => LeafletMap;
    tileLayer: (url: string, options: Record<string, unknown>) => { addTo: (target: unknown) => void };
    layerGroup: () => LeafletLayerGroup;
    marker: (coords: [number, number]) => LeafletMarker;
    latLngBounds: (coords: Array<[number, number]>) => unknown;
};

const DEFAULT_CENTER: [number, number] = [58.8856, 25.5571];
const DEFAULT_ZOOM = 7;

const props = defineProps<{
    weather: WeatherPayload;
    city: string;
    resolvedCity: string | null;
    weatherError: string | null;
    markers: Marker[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const weatherForm = useForm({
    city: props.city,
});

const manualMarkerForm = useForm({
    name: '',
    latitude: '',
    longitude: '',
    description: '',
});

const markerForm = useForm({
    name: '',
    latitude: '',
    longitude: '',
    description: '',
});

const mapContainer = ref<HTMLElement | null>(null);
const leaflet = ref<LeafletStatic | null>(null);
const leafletMap = ref<LeafletMap | null>(null);
const leafletMarkers = ref<LeafletLayerGroup | null>(null);
const markerInstances = ref<Record<number, LeafletMarker>>({});

const isMarkerDialogOpen = ref(false);
const isEditingMarker = ref(false);
const selectedMarkerId = ref<number | null>(null);

const dialogTitle = computed(() =>
    isEditingMarker.value ? 'Muuda markerit' : 'Lisa uus marker',
);
const dialogDescription = computed(() =>
    isEditingMarker.value
        ? 'Uuenda markeri nime, kirjeldust või koordinaate.'
        : 'Salvesta kaardil valitud asukoht uue markerina.',
);

const searchWeather = () => {
    weatherForm.get('/dashboard', {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetMarkerForm = () => {
    markerForm.reset();
    markerForm.clearErrors();
    selectedMarkerId.value = null;
    isEditingMarker.value = false;
};

const openCreateMarkerDialog = (latitude: number, longitude: number) => {
    manualMarkerForm.reset();
    manualMarkerForm.clearErrors();
    manualMarkerForm.latitude = latitude.toFixed(6);
    manualMarkerForm.longitude = longitude.toFixed(6);
    isEditingMarker.value = false;
    isMarkerDialogOpen.value = true;
};

const openEditMarkerDialog = (marker: Marker) => {
    markerForm.clearErrors();
    selectedMarkerId.value = marker.id;
    isEditingMarker.value = true;
    markerForm.name = marker.name;
    markerForm.latitude = marker.latitude.toFixed(6);
    markerForm.longitude = marker.longitude.toFixed(6);
    markerForm.description = marker.description ?? '';
    isMarkerDialogOpen.value = true;
};

const closeMarkerDialog = (isOpen = false) => {
    isMarkerDialogOpen.value = isOpen;

    if (!isOpen) {
        resetMarkerForm();
        manualMarkerForm.clearErrors();
        manualMarkerForm.reset();
    }
};

const submitMarker = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => closeMarkerDialog(false),
    };

    if (isEditingMarker.value && selectedMarkerId.value !== null) {
        markerForm.put(`/markers/${selectedMarkerId.value}`, options);
        return;
    }

    markerForm.post('/markers', options);
};

const submitManualMarker = () => {
    manualMarkerForm.post('/markers', {
        preserveScroll: true,
        onSuccess: () => {
            manualMarkerForm.reset();
            isMarkerDialogOpen.value = false;
        },
    });
};

const deleteMarker = (marker: Marker) => {
    if (!window.confirm(`Kas kustutada marker "${marker.name}"?`)) {
        return;
    }

    router.delete(`/markers/${marker.id}`, {
        preserveScroll: true,
    });
};

const deleteSelectedMarker = () => {
    if (selectedMarkerId.value === null) {
        return;
    }

    deleteMarker({
        id: selectedMarkerId.value,
        name: markerForm.name,
        latitude: Number(markerForm.latitude || 0),
        longitude: Number(markerForm.longitude || 0),
        description: markerForm.description || null,
        added: null,
        edited: null,
    });
};

const escapeHtml = (value: string) =>
    value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const renderMarkers = (shouldFitBounds = false) => {
    if (!leaflet.value || !leafletMap.value || !leafletMarkers.value) {
        return;
    }

    leafletMarkers.value.clearLayers();
    markerInstances.value = {};

    const bounds: Array<[number, number]> = [];

    props.markers.forEach((marker) => {
        const popupDescription = marker.description
            ? `<p style="margin:6px 0 0">${escapeHtml(marker.description)}</p>`
            : '';
        const popupContent = `<strong>${escapeHtml(marker.name)}</strong>${popupDescription}`;
        const instance = leaflet.value!
            .marker([marker.latitude, marker.longitude])
            .addTo(leafletMarkers.value!)
            .bindPopup(popupContent)
            .on('click', () => {
                openEditMarkerDialog(marker);
            });

        markerInstances.value[marker.id] = instance;
        bounds.push([marker.latitude, marker.longitude]);
    });

    if (!shouldFitBounds) {
        return;
    }

    if (bounds.length === 0) {
        leafletMap.value.setView(DEFAULT_CENTER, DEFAULT_ZOOM);
        return;
    }

    if (bounds.length === 1) {
        leafletMap.value.setView(bounds[0], 13);
        return;
    }

    leafletMap.value.fitBounds(leaflet.value.latLngBounds(bounds), {
        padding: [36, 36],
    });
};

const initialiseMap = async () => {
    if (!mapContainer.value || leafletMap.value) {
        return;
    }

    leaflet.value = await loadLeaflet();

    const map = leaflet.value.map(mapContainer.value);
    leaflet.value
        .tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        })
        .addTo(map);

    const layers = leaflet.value.layerGroup().addTo(map);

    map.on('click', ({ latlng }) => {
        selectedMarkerId.value = null;
        openCreateMarkerDialog(latlng.lat, latlng.lng);
    });

    leafletMap.value = map;
    leafletMarkers.value = layers;

    renderMarkers(true);
};

watch(
    () => props.markers,
    (current, previous) => {
        const shouldFitBounds = current.length !== previous?.length;
        renderMarkers(shouldFitBounds);
    },
    { deep: true },
);

onMounted(() => {
    initialiseMap();
});

onBeforeUnmount(() => {
    leafletMap.value?.off();
    leafletMap.value?.remove();
});

async function loadLeaflet(): Promise<LeafletStatic> {
    if (typeof window === 'undefined') {
        throw new Error('Leaflet requires a browser environment.');
    }

    const existing = (window as Window & { L?: LeafletStatic }).L;
    if (existing) {
        return existing;
    }

    await Promise.all([ensureLeafletStylesheet(), ensureLeafletScript()]);

    const loaded = (window as Window & { L?: LeafletStatic }).L;
    if (!loaded) {
        throw new Error('Leaflet failed to load.');
    }

    return loaded;
}

function ensureLeafletStylesheet(): Promise<void> {
    if (document.getElementById('leaflet-styles')) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const link = document.createElement('link');
        link.id = 'leaflet-styles';
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
        link.crossOrigin = '';
        link.onload = () => resolve();
        link.onerror = () => reject(new Error('Leaflet CSS failed to load.'));
        document.head.appendChild(link);
    });
}

function ensureLeafletScript(): Promise<void> {
    if (document.getElementById('leaflet-script')) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.id = 'leaflet-script';
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
        script.crossOrigin = '';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Leaflet script failed to load.'));
        document.body.appendChild(script);
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Dialog :open="isMarkerDialogOpen" @update:open="closeMarkerDialog">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>{{ dialogDescription }}</DialogDescription>
                </DialogHeader>

                <form v-if="isEditingMarker" class="space-y-4" @submit.prevent="submitMarker">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Nimi</label>
                        <Input v-model="markerForm.name" placeholder="Näiteks Tallinna vanalinn" />
                        <InputError :message="markerForm.errors.name" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Laiuskraad</label>
                            <Input v-model="markerForm.latitude" placeholder="59.437000" />
                            <InputError :message="markerForm.errors.latitude" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Pikkuskraad</label>
                            <Input v-model="markerForm.longitude" placeholder="24.745000" />
                            <InputError :message="markerForm.errors.longitude" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Kirjeldus</label>
                        <Textarea
                            v-model="markerForm.description"
                            placeholder="Lisa lühike kirjeldus, miks see koht oluline on."
                        />
                        <InputError :message="markerForm.errors.description" />
                    </div>

                    <DialogFooter class="gap-2">
                        <Button type="button" variant="outline" @click="closeMarkerDialog(false)">
                            Tühista
                        </Button>
                        <Button
                            type="button"
                            variant="destructive"
                            @click="deleteSelectedMarker"
                        >
                            Kustuta
                        </Button>
                        <Button type="submit" :disabled="markerForm.processing">
                            {{ markerForm.processing ? 'Salvestan...' : 'Salvesta marker' }}
                        </Button>
                    </DialogFooter>
                </form>

                <form v-else class="space-y-4" @submit.prevent="submitManualMarker">
                    <div
                        class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-700 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300"
                    >
                        Kaardilt valitud punkt: {{ manualMarkerForm.latitude }}, {{ manualMarkerForm.longitude }}
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Nimi</label>
                        <Input v-model="manualMarkerForm.name" placeholder="Näiteks Tallinna vanalinn" />
                        <InputError :message="manualMarkerForm.errors.name" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Laiuskraad</label>
                            <Input v-model="manualMarkerForm.latitude" placeholder="59.437000" />
                            <InputError :message="manualMarkerForm.errors.latitude" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Pikkuskraad</label>
                            <Input v-model="manualMarkerForm.longitude" placeholder="24.745000" />
                            <InputError :message="manualMarkerForm.errors.longitude" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Kirjeldus</label>
                        <Textarea
                            v-model="manualMarkerForm.description"
                            placeholder="Lisa lühike kirjeldus, miks see koht oluline on."
                        />
                        <InputError :message="manualMarkerForm.errors.description" />
                    </div>

                    <DialogFooter class="gap-2">
                        <Button type="button" variant="outline" @click="closeMarkerDialog(false)">
                            Tühista
                        </Button>
                        <Button type="submit" :disabled="manualMarkerForm.processing">
                            {{ manualMarkerForm.processing ? 'Salvestan...' : 'Salvesta marker' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <div
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 p-4 shadow-sm dark:border-sidebar-border"
                >
                    <div v-if="weather" class="flex h-full min-h-52 flex-col justify-between gap-6">
                        <div class="flex justify-between gap-4">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">
                                    {{ resolvedCity || city }}
                                </h3>
                                <p class="mt-1 text-4xl font-black">{{ Math.round(weather.main.temp) }}°C</p>
                                <p class="text-xs capitalize text-muted-foreground">
                                    {{ weather.weather[0].description }}
                                </p>
                            </div>
                            <div class="rounded-full bg-white/50 dark:bg-black/20">
                                <img
                                    :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png`"
                                    class="h-16 w-16"
                                    alt="Weather icon"
                                />
                            </div>
                        </div>

                        <form class="flex gap-2" @submit.prevent="searchWeather">
                            <input
                                v-model="weatherForm.city"
                                type="text"
                                class="h-9 flex-1 rounded-lg border border-input bg-background/50 px-3 py-1 text-sm shadow-sm outline-none focus:ring-2 focus:ring-blue-500 dark:text-white"
                                placeholder="Otsi linna..."
                            />
                            <button
                                type="submit"
                                :disabled="weatherForm.processing"
                                class="inline-flex cursor-pointer items-center justify-center rounded-lg bg-blue-600 px-3 py-1 text-xs font-bold text-white shadow transition-all hover:bg-blue-700 disabled:opacity-50"
                            >
                                <span v-if="weatherForm.processing">...</span>
                                <span v-else>OK</span>
                            </button>
                        </form>
                    </div>

                    <div v-else class="flex min-h-52 flex-col items-center justify-center p-4 text-center">
                        <p class="mb-2 text-xs font-bold uppercase text-red-500">
                            {{ weatherError || `Linna "${city}" ei leitud.` }}
                        </p>
                        <button
                            class="text-[10px] uppercase text-gray-500 underline transition-colors hover:text-blue-600"
                            @click="weatherForm.city = 'Tallinn'; searchWeather()"
                        >
                            Tagasi Tallinna
                        </button>
                    </div>
                </div>

                <Link
                    :href="postsIndex().url"
                    class="group relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 p-4 shadow-sm transition hover:border-emerald-500/40 hover:shadow-md dark:border-sidebar-border"
                >
                    <div class="flex h-full min-h-52 flex-col justify-between gap-6">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                Blogi
                            </p>
                            <h3 class="mt-2 text-2xl font-black">Postitused ja kommentaarid</h3>
                            <p class="mt-3 text-sm text-muted-foreground">
                                Ava blogivaade, kus saad lisada, muuta, kustutada ja kommenteerida postitusi.
                            </p>
                        </div>

                        <div class="flex items-center justify-between text-sm font-medium">
                            <span class="text-muted-foreground">Ava blogi</span>
                            <span class="transition group-hover:translate-x-1">→</span>
                        </div>
                    </div>
                </Link>
            </div>

            <section class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Kaart ja markerid</h2>
                        <p class="text-sm text-muted-foreground">
                            Klõps tühjal kaardil lisab uue markeri. Olemasoleva markeri peale klõpsates saad selle kohe muuta või kustutada.
                        </p>
                    </div>
                </div>

                <div>
                    <div
                        ref="mapContainer"
                        class="h-[620px] overflow-hidden rounded-xl border border-border bg-slate-100"
                    />
                </div>
            </section>
        </div>
    </AppLayout>
</template>
