<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

// Props
const props = defineProps<{
    post: {
        id: number;
        title: string;
        content: string;
        author_id: number | null;
        published: boolean | number | null;
        created_at?: string;
        updated_at?: string;
    };
    authors: Record<string, string>; // { "1": "Alice" }
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Posts', href: index().url },
    {
        title: `Edit Post #${props.post.id}`,
        href: `/posts/${props.post.id}/edit`,
    },
];

// Convert authors object to array for Select
const authorEntries = computed(() =>
    Object.entries(props.authors).map(([id, name]) => ({
        id: String(id),
        name,
    })),
);

// Inertia form
const form = useForm({
    title: props.post.title ?? '',
    content: props.post.content ?? '',
    author_id: props.post.author_id ?? null,
    published: Boolean(props.post.published),
});

// String intermediary for Select
const authorString = ref(
    form.author_id !== null && form.author_id !== undefined
        ? String(form.author_id)
        : '',
);

// Watch: update form.author_id whenever Select changes
watch(authorString, (val) => {
    form.author_id = val ? Number(val) : null;
});

// Keep authorString in sync if form.author_id changes programmatically
watch(
    () => form.author_id,
    (val) => {
        authorString.value =
            val !== null && val !== undefined ? String(val) : '';
    },
);

// Submit
function submit() {
    form.put(`/posts/${props.post.id}`, {
        preserveScroll: true,
        onSuccess: () => console.log('Post updated!'),
        onError: (errors) => console.warn('Server validation errors:', errors),
    });
}
</script>

<template>
    <Head :title="`Edit Post #${props.post.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-2xl flex-col gap-6 p-6">
            <h1 class="text-2xl font-semibold">Edit Post</h1>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <!-- Title -->
                <div>
                    <Label for="title">Title</Label>
                    <input
                        id="title"
                        v-model="form.title"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.title" class="text-sm text-red-600">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Author Select -->
                <div>
                    <Label>Author</Label>
                    <Select v-model="authorString">
                        <SelectTrigger class="mt-1 w-full text-left">
                            <SelectValue placeholder="Select an author" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="a in authorEntries"
                                :key="a.id"
                                :value="a.id"
                            >
                                {{ a.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="form.errors.author_id"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.author_id }}
                    </p>
                </div>

                <!-- Content -->
                <div>
                    <Label for="content">Content</Label>
                    <Textarea id="content" rows="6" v-model="form.content" />
                    <p v-if="form.errors.content" class="text-sm text-red-600">
                        {{ form.errors.content }}
                    </p>
                </div>

                <!-- Published -->
                <div class="flex items-center justify-between">
                    <Label>Published</Label>
                    <Switch v-model="form.published" />
                </div>

                <!-- Metadata -->
                <div class="mt-2 text-sm text-gray-500">
                    <p>Created: {{ props.post.created_at }}</p>
                    <p>Updated: {{ props.post.updated_at }}</p>
                </div>

                <!-- Buttons -->
                <div class="mt-4 flex gap-3">
                    <Button type="submit" :disabled="form.processing"
                        >Save</Button
                    >
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(index().url)"
                        >Cancel</Button
                    >
                </div>
            </form>
        </div>
    </AppLayout>
</template>
