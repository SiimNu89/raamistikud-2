<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { add } from '@/routes/comments';
import { edit, index } from '@/routes/posts';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

type Comment = {
    id: number;
    content: string;
    user: {
        id: number;
        name: string;
    } | null;
};

const props = defineProps<{
    post: {
        id: number;
        title: string;
        description: string;
        created_at: string | null;
        updated_at: string | null;
        created_at_formated: string | null;
        updated_at_formated: string | null;
        comments: Comment[];
    };
}>();

const page = usePage<AppPageProps>();
const isAdmin = Boolean(page.props.auth.user?.is_admin);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Blog', href: index().url },
    { title: props.post.title, href: `/posts/${props.post.id}` },
];

const form = useForm({
    content: '',
});

const submit = () => {
    form.post(add(props.post.id).url, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const deleteComment = (commentId: number) => {
    if (!confirm('Delete this comment?')) return;

    router.delete(`/comments/${commentId}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="props.post.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl space-y-6 p-6">
            <header class="space-y-2">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold">{{ props.post.title }}</h1>
                        <p class="text-sm text-muted-foreground">
                            Published {{ props.post.created_at_formated || props.post.created_at }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <Button as-child variant="outline">
                            <Link :href="edit(props.post.id).url">Edit post</Link>
                        </Button>
                        <Button as-child variant="ghost">
                            <Link :href="index().url">Back to blog</Link>
                        </Button>
                    </div>
                </div>
            </header>

            <section class="rounded-xl border p-6">
                <p class="whitespace-pre-line text-base leading-7">{{ props.post.description }}</p>
            </section>

            <section class="space-y-4">
                <div>
                    <h2 class="text-xl font-semibold">Comments</h2>
                    <p class="text-sm text-muted-foreground">
                        Logged-in users can add comments. Admin can remove comments.
                    </p>
                </div>

                <div v-if="props.post.comments.length" class="space-y-3">
                    <article
                        v-for="comment in props.post.comments"
                        :key="comment.id"
                        class="rounded-xl border p-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-medium">{{ comment.user?.name || 'Guest' }}</p>
                                <p class="mt-2 text-sm text-muted-foreground">{{ comment.content }}</p>
                            </div>

                            <Button
                                v-if="isAdmin"
                                size="sm"
                                variant="destructive"
                                @click="deleteComment(comment.id)"
                            >
                                Delete
                            </Button>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="rounded-xl border border-dashed p-6 text-sm text-muted-foreground"
                >
                    No comments yet.
                </div>

                <form class="space-y-3 rounded-xl border p-4" @submit.prevent="submit">
                    <Textarea
                        v-model="form.content"
                        placeholder="Add a comment..."
                        class="min-h-24"
                    />
                    <p v-if="form.errors.content" class="text-sm text-red-600">
                        {{ form.errors.content }}
                    </p>
                    <div class="flex justify-end">
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Posting...' : 'Post comment' }}
                        </Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
