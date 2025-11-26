<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, edit, index, show } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Posts', href: index().url }];

// Props
interface Author {
    first_name: string;
    last_name: string;
}

interface Post {
    id: number;
    title: string;
    content: string;
    author: Author | null;
    published: boolean;
    created_at: string | null;
    updated_at: string | null;
    created_at_formatted: string | null;
    updated_at_formatted: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    page?: number | null;
    active: boolean;
}

interface PaginatedResponse {
    current_page: number;
    data: Post[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

defineProps<{
    posts: PaginatedResponse;
}>();

// Delete post
function deletePost(postId: number) {
    if (!confirm('Are you sure you want to delete this post?')) return;
    router.delete(destroy.url(postId), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Table>
                <TableCaption>A list of your recent blog posts.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">ID</TableHead>
                        <TableHead>Title</TableHead>
                        <TableHead>Author</TableHead>
                        <TableHead class="text-right">Created at</TableHead>
                        <TableHead class="text-right">Updated at</TableHead>
                        <TableHead class="text-right">Published</TableHead>
                        <TableHead
                            ><span class="sr-only">Actions</span></TableHead
                        >
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="post in posts.data" :key="post.id">
                        <TableCell class="font-medium">{{ post.id }}</TableCell>
                        <TableCell>{{ post.title }}</TableCell>
                        <TableCell>
                            {{ post.author?.first_name ?? '' }}
                            {{ post.author?.last_name ?? '' }}
                        </TableCell>
                        <TableCell class="text-right">{{
                            post.created_at_formatted ?? '-'
                        }}</TableCell>
                        <TableCell class="text-right">{{
                            post.updated_at_formatted ?? '-'
                        }}</TableCell>
                        <TableCell class="text-right">
                            <span
                                :class="
                                    post.published
                                        ? 'text-green-600'
                                        : 'text-gray-400'
                                "
                            >
                                {{ post.published ? 'Yes' : 'No' }}
                            </span>
                        </TableCell>

                        <TableCell>
                            <div class="flex justify-end">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="icon" variant="ghost">
                                            <MoreVertical />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent>
                                        <DropdownMenuItem
                                            @click="
                                                router.visit(show(post.id).url)
                                            "
                                        >
                                            Show
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            @click="
                                                router.visit(edit(post.id).url)
                                            "
                                        >
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            class="text-destructive"
                                            @click="deletePost(post.id)"
                                        >
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
