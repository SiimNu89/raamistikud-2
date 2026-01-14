<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { add } from '@/routes/comments';
import { edit, index } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Post } from './Index.vue';
import { on } from 'events';

const props = defineProps<{
    post: Post;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Posts', href: index().url },
    { title: props.post.title, href: `/posts/${props.post.id}` },
];

const form = useForm({
    content: '',
});

const submit = () => {
    form.post(add(props.post.id).url);
    preservescrll.
    
    onSuccess(() => {
        form.reset();
    });
};
</script>

<template>
    <Head :title="props.post.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <!-- Post Header -->
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
                        props.post.created_at_formated || props.post.created_at
                    }}
                </p>
                <p class="text-sm">
                    Last updated:
                    {{
                        props.post.updated_at_formated || props.post.updated_at
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

            <!-- Post Content -->
            <section class="prose prose-slate">
                <p class="whitespace-pre-line">{{ props.post.content }}</p>
            </section>

            <!-- Action Buttons -->
            <div class="mt-4 flex gap-2">
                <Button as-child variant="outline">
                    <Link :href="edit.url(props.post.id)">Edit Post</Link>
                </Button>
                <Button variant="ghost" @click="goBack">Back to Posts</Button>
            </div>

            <!-- Comments List -->
            <div class="mt-6">
                <h2 class="mb-2 text-xl font-semibold">Comments</h2>

                <ul v-if="post.comments" class="space-y-2">
                    <li
                        v-for="comment in post.comments"
                        :key="comment.id"
                        class="rounded border p-2"
                    >
                        <strong>{{ comment.user?.name || 'Guest' }}:</strong>
                        {{ comment.content }}
                    </li>
                </ul>
                <p v-else class="text-gray-500">No comments yet.</p>

                <!-- Add Comment Form -->
                <div class="mt-4 flex gap-2">
                    <form @submit.prevent="submit">
                        <Textarea
                            v-model="form.content"
                            type="text"
                            placeholder="Add a comment..."
                            class="flex-1 rounded border p-2"
                        />
                        <Button type="submit">Submit</Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
