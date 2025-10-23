<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Switch from '@/components/ui/switch/Switch.vue';
import Button from '@/components/ui/button/Button.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import InputError from '@/components/InputError.vue';




const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts create',
        href: create().url,
    },
];

const form = useForm({
    title: '',
    content: '',
    author: '',
    published: false,
});

const submit = () => {
    form.post(store().url)
}

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="max-w-2xl h-full mx-auto w-full">
                <h3 class="text-lg font-medium">Post Create</h3>
                <form @submit.prevent="submit">
                    <div class="grid gap-4 mt-6">
                        <div>
                            <Label for="title">Title</Label>
                            <Input class="mt-1" name="title" v-model="form.title" />
                            <InputError :message="form.errors.title"></InputError>
                        </div>
                        <div>
                            <Label for="content">Content</Label>
                            <Textarea class="mt-1" name="title" v-model="form.content" />
                            <InputError :message="form.errors.content"></InputError>
                        </div>
                        <div>
                            <Label for="author">Author</Label>
                            <Input class="mt-1" name="title" v-model="form.author" />
                            <InputError :message="form.errors.author"></InputError>
                        </div>
                        <div className="flex items-center space-x-2">
                            <Switch id="published" />
                            <Label For="published">Published</Label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <Button type="submit">Save</Button>
                    </div>
                </form>
                <pre>{{ form }}</pre>
            </div>
        </div>
    </AppLayout>
</template>