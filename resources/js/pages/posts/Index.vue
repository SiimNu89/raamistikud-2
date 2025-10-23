<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
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

import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Posts', href: index().url }];

export interface PaginationLink {
    url: string | null;
    label: string;
    page?: number | null;
    active: boolean;
}

export interface PaginatedResponse {
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

type Post = {
    id: number;
    title: string;
    content: string;
    author: string;
    published: boolean;
    created_at: string;
    updated_at: string;
    created_at_formatted: string;
    updated_at_formatted: string;
};

const props = defineProps<{ posts: PaginatedResponse }>();
function goToPage(page: number) {
    router.get(
        `${props.posts.path}?page=${page}`,
        {},
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-6 overflow-x-auto rounded-xl p-6">
            <h1 class="text-2xl font-semibold">Posts</h1>

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
                        <TableHead></TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="post in props.posts.data" :key="post.id">
                        <TableCell class="font-medium">{{ post.id }}</TableCell>
                        <TableCell>{{ post.title }}</TableCell>
                        <TableCell>{{ post.author }}</TableCell>
                        <TableCell class="text-right">{{
                            post.created_at_formatted
                        }}</TableCell>
                        <TableCell class="text-right">{{
                            post.updated_at_formatted
                        }}</TableCell>
                        <TableCell class="text-right">
                            <span
                                :class="[
                                    post.published
                                        ? 'text-green-600'
                                        : 'text-gray-400',
                                    'font-medium',
                                ]"
                            >
                                {{ post.published ? 'Yes' : 'No' }}
                            </span>
                        </TableCell>

                        <TableCell class="text-right">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button size="icon" variant="ghost">
                                        <MoreVertical />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                    <DropdownMenuLabel
                                        >Actions</DropdownMenuLabel
                                    >
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem>Edit</DropdownMenuItem>
                                    <DropdownMenuItem>Delete</DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            
            <Pagination
                v-slot="{ page }"
                :items-per-page="props.posts.per_page"
                :total="props.posts.total"
                :default-page="props.posts.current_page"
            >
                <PaginationContent
                    v-slot="{ items }"
                    class="mt-4 justify-center"
                >
                    <PaginationPrevious
                        :disabled="!props.posts.prev_page_url"
                        @click="goToPage(props.posts.current_page - 1)"
                    />

                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem
                            v-if="item.type === 'page'"
                            :value="item.value"
                            :is-active="item.value === page"
                            @click="goToPage(item.value)"
                        >
                            {{ item.value }}
                        </PaginationItem>
                    </template>

                    <PaginationEllipsis :index="4" />

                    <PaginationNext
                        :disabled="!props.posts.next_page_url"
                        @click="goToPage(props.posts.current_page + 1)"
                    />
                </PaginationContent>
            </Pagination>
        </div>
    </AppLayout>
</template>
