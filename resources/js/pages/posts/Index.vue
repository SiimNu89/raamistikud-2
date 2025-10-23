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
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: index().url,
    },
];

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

defineProps<{
    posts: Post[];
}>();
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- <pre>{{ posts }}</pre> -->
            <ul>
                <li v-for="(post, index) in posts" :key="index">
                    {{ post.title }}
                </li>
            </ul>
            <Table>
                <TableCaption>A list of your recent blog posts.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> ID </TableHead>
                        <TableHead>Title</TableHead>
                        <TableHead>Author</TableHead>
                        <TableHead class="text-right"> Created at </TableHead>
                        <TableHead class="text-right"> Updated At</TableHead>
                        <TableHead class="text-right"> Published </TableHead>
                        <TableHead>
                            <span class="sr-only">Actions</span>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="post in posts" :key="post.id">
                        <TableCell class="font-medium">
                            {{ post.id }}
                        </TableCell>
                        <TableCell>{{ post.title }}</TableCell>
                        <TableCell>{{ post.author }}</TableCell>
                        <TableCell>{{ post.created_at_formatted }}</TableCell>
                        <TableCell>{{ post.updated_at_formatted }}</TableCell>
                        <TableCell>{{ post.published }}</TableCell>
                        <TableCell class="text-right"> .00 </TableCell>
                        <TableCell>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button size="icon" variant_="ghost">
                                        <MoreVertical />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                    <DropdownMenuLabel>View</DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem>Edit</DropdownMenuItem>
                                    <DropdownMenuItem
                                        >Subscription</DropdownMenuItem
                                    >
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
