<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, index, store } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: index().url,
    },
    {
        title: 'New post',
        href: create().url,
    },
];

const form = useForm({
    title: '',
    description: '',
});

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <Head title="Create Blog Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-3xl flex-col gap-6 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">New blog post</h1>
                    <p class="text-sm text-muted-foreground">
                        Create a new blog entry with a title and description.
                    </p>
                </div>

                <Button as-child variant="outline">
                    <Link :href="index().url">Back to blog</Link>
                </Button>
            </div>

            <form class="space-y-5 rounded-xl border p-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" v-model="form.description" class="min-h-48" />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save post' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
