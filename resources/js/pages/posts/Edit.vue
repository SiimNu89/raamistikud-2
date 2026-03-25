<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    post: {
        id: number;
        title: string;
        description: string;
        created_at?: string | null;
        updated_at?: string | null;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Blog', href: index().url },
    { title: props.post.title, href: show(props.post.id).url },
    { title: 'Edit', href: `/posts/${props.post.id}/edit` },
];

const form = useForm({
    title: props.post.title,
    description: props.post.description,
});

const submit = () => {
    form.put(`/posts/${props.post.id}`);
};
</script>

<template>
    <Head :title="`Edit ${props.post.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-3xl flex-col gap-6 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">Edit blog post</h1>
                    <p class="text-sm text-muted-foreground">
                        Update the title or description of your post.
                    </p>
                </div>

                <Button as-child variant="outline">
                    <Link :href="show(props.post.id).url">Back to post</Link>
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

                <div class="text-sm text-muted-foreground">
                    <p>Created: {{ props.post.created_at ?? '-' }}</p>
                    <p>Updated: {{ props.post.updated_at ?? '-' }}</p>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Update post' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
