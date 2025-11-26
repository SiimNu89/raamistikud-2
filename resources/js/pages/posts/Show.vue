<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    post: {
        id: number;
        title: string;
        content: string;
        author?: { first_name: string; last_name: string } | null;
        published: boolean;
        created_at: string;
        updated_at: string;
        created_at_formatted?: string;
        updated_at_formatted?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Posts', href: index().url },
    { title: props.post.title, href: `/posts/${props.post.id}` },
];

function goBack() {
    router.visit(index().url);
}
</script>

<template>
    <Head :title="props.post.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <header class="space-y-2">
                <h1 class="text-3xl font-bold">{{ props.post.title }}</h1>
                <p class="text-sm text-gray-500">
                    Written by
                    <span class="font-medium">
                        {{ props.post.author?.first_name || 'Unknown' }}
                        {{ props.post.author?.last_name || '' }}
                    </span>
                    • Published on
                    {{
                        props.post.created_at_formatted || props.post.created_at
                    }}
                </p>
                <p class="text-sm">
                    Last updated:
                    {{
                        props.post.updated_at_formatted || props.post.updated_at
                    }}
                </p>
                <p class="text-sm">
                    Status:
                    <span
                        :class="
                            props.post.published
                                ? 'text-green-600'
                                : 'text-gray-500'
                        "
                    >
                        {{ props.post.published ? 'Published' : 'Draft' }}
                    </span>
                </p>
            </header>

            <section class="prose prose-slate">
                <p class="whitespace-pre-line">{{ props.post.content }}</p>
            </section>

            <div class="mt-4 flex gap-2">
                <Button as-child variant="outline">
                    <Link :href="edit.url(props.post.id)">Edit Post</Link>
                </Button>
                <Button variant="ghost" @click="goBack">Back to Posts</Button>
            </div>
        </div>
    </AppLayout>
</template>
