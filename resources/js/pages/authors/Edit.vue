<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    author: {
        id: number;
        first_name: string;
        last_name: string;
        birth_date: string | null;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Authors', href: '/authors' },
    {
        title: `Edit Author #${props.author.id}`,
        href: `/authors/${props.author.id}/edit`,
    },
];

const form = useForm({
    first_name: props.author.first_name,
    last_name: props.author.last_name,
    birth_date: props.author.birth_date ?? '',
});

function submit() {
    form.put(`/authors/${props.author.id}`, {
        preserveScroll: true,
        onSuccess: () => console.log('Author updated!'),
    });
}
</script>

<template>
    <Head :title="`Edit Author #${props.author.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-2xl flex-col gap-6 p-6">
            <h1 class="text-2xl font-semibold">Edit Author</h1>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <Label for="first_name">First Name</Label>
                    <Input id="first_name" v-model="form.first_name" />
                </div>

                <div>
                    <Label for="last_name">Last Name</Label>
                    <Input id="last_name" v-model="form.last_name" />
                </div>

                <div>
                    <Label for="birth_date">Birth Date</Label>
                    <Input
                        id="birth_date"
                        type="date"
                        v-model="form.birth_date"
                    />
                </div>

                <div class="mt-4 flex gap-3">
                    <Button type="submit" :disabled="form.processing"
                        >Save Changes</Button
                    >
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit('/authors')"
                        >Cancel</Button
                    >
                </div>
            </form>
        </div>
    </AppLayout>
</template>
