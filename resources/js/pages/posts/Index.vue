<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, show } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

export type Post = {
    id: number;
    title: string;
    description: string;
    created_at: string | null;
    updated_at: string | null;
    created_at_formated: string | null;
    updated_at_formated: string | null;
};

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedResponse {
    data: Post[];
    links: PaginationLink[];
}

defineProps<{
    posts: PaginatedResponse;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Blog', href: index().url }];

function deletePost(postId: number) {
    if (!confirm('Are you sure you want to delete this blog post?')) return;
    router.delete(destroy.url(postId), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Blog" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold">Blog</h1>
                    <p class="text-sm text-muted-foreground">
                        Blog posts table with the required fields: id, title, description, created_at, updated_at.
                    </p>
                </div>

                <Button as-child>
                    <Link :href="create().url">New post</Link>
                </Button>
            </div>

            <div v-if="posts.data.length" class="rounded-xl border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Title</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Updated At</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="post in posts.data" :key="post.id">
                            <TableCell class="font-medium">{{ post.id }}</TableCell>
                            <TableCell>{{ post.title }}</TableCell>
                            <TableCell class="max-w-xl whitespace-pre-line text-sm text-muted-foreground">
                                {{ post.description }}
                            </TableCell>
                            <TableCell>{{ post.created_at_formated ?? post.created_at ?? '-' }}</TableCell>
                            <TableCell>{{ post.updated_at_formated ?? post.updated_at ?? '-' }}</TableCell>
                            <TableCell>
                                <div class="flex justify-end gap-2">
                                    <Button as-child size="sm" variant="outline">
                                        <Link :href="show(post.id).url">Open</Link>
                                    </Button>
                                    <Button as-child size="sm" variant="outline">
                                        <Link :href="edit(post.id).url">Edit</Link>
                                    </Button>
                                    <Button size="sm" variant="destructive" @click="deletePost(post.id)">
                                        Delete
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground"
            >
                Blog posts are still empty. Run the seeder to generate fake posts.
            </div>
        </div>
    </AppLayout>
</template>
