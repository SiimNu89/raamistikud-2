<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectItem, SelectValue, SelectTrigger, SelectContent } from '@/components/ui/select';
import Switch from '@/components/ui/switch/Switch.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts create',
        href: create().url,
    },
];

const { authors } = defineProps<{
    authors: Record<number, string>;
}>();

const form = useForm({
    title: '',
    content: '',
    author_id: null,
    published: false,
});

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto h-full w-full max-w-2xl">
                <h3 class="text-lg font-medium">Post Create</h3>

                <form @submit.prevent="submit">
                    <div class="mt-6 grid gap-4">
                        
                        <!-- Title -->
                        <div>
                            <Label for="title">Title</Label>
                            <Input class="mt-1" name="title" v-model="form.title" />
                            <InputError :message="form.errors.title" />
                        </div>

                        <!-- Content -->
                        <div>
                            <Label for="content">Content</Label>
                            <Textarea class="mt-1" name="content" v-model="form.content" />
                            <InputError :message="form.errors.content" />
                        </div>

                        <!-- Author -->
                        <div>
                            <Label for="author_id">Author</Label>

                            <Select v-model="form.author_id">
                                <SelectTrigger class="mt-1 w-full text-left">
                                    <SelectValue placeholder="Select an author" />
                                </SelectTrigger>

                                <SelectContent>
                                    <SelectItem
                                        v-for="(name, id) in authors"
                                        :key="id"
                                        :value="id"
                                    >
                                        {{ name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <InputError :message="form.errors.author_id" />
                        </div>

                        <!-- Published -->
                        <div class="flex items-center space-x-2">
                            <Switch id="published" v-model:checked="form.published" />
                            <Label for="published">Published</Label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <Button type="submit">Save</Button>
                    </div>
                </form>

                <!-- Debug -->
                <pre>{{ form }}</pre>
            </div>
        </div>
    </AppLayout>
</template>
