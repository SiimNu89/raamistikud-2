<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { router, Head } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

// Props: paginated authors response
interface Author {
    id: number;
    first_name: string;
    last_name: string;
    birth_date: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedAuthors {
    current_page: number;
    data: Author[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: { url: string | null; label: string; active: boolean }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

const props = defineProps<{
    authors: PaginatedAuthors;
}>();

// Delete author
function deleteAuthor(id: number) {
    if (confirm('Are you sure you want to delete this author?')) {
        router.delete(`/authors/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Authors" />

    <AppLayout>
        <div class="p-4">
            <div class="flex justify-between mb-4">
                <h1 class="text-xl font-semibold">Authors</h1>
                <Button @click="router.visit('/authors/create')">New Author</Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>First Name</TableHead>
                        <TableHead>Last Name</TableHead>
                        <TableHead>Birth Date</TableHead>
                        <TableHead>Created At</TableHead>
                        <TableHead>Updated At</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="author in props.authors.data" :key="author.id">
                        <TableCell>{{ author.first_name }}</TableCell>
                        <TableCell>{{ author.last_name }}</TableCell>
                        <TableCell>{{ author.birth_date ?? '-' }}</TableCell>

                        <!-- Added fields -->
                        <TableCell>{{ author.created_at }}</TableCell>
                        <TableCell>{{ author.updated_at }}</TableCell>

                        <TableCell>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="icon">
                                        <MoreVertical />
                                    </Button>
                                </DropdownMenuTrigger>

                                <DropdownMenuContent>
                                    <DropdownMenuItem @click="router.visit(`/authors/${author.id}/edit`)">
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuItem class="text-destructive" @click="deleteAuthor(author.id)">
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <div class="flex justify-center mt-4 items-center gap-2">
                <Button
                    :disabled="!props.authors.prev_page_url"
                    @click="props.authors.prev_page_url && router.get(props.authors.prev_page_url)"
                >
                    Previous
                </Button>

                <span>{{ props.authors.current_page }} / {{ props.authors.last_page }}</span>

                <Button
                    :disabled="!props.authors.next_page_url"
                    @click="props.authors.next_page_url && router.get(props.authors.next_page_url)"
                >
                    Next
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
