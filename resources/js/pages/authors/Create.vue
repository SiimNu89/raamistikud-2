<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AuthorForm from '../authors/AuthorForm.vue';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Create Author" />
    <AppLayout>
        <div class="p-4">
            <h1 class="text-xl font-semibold mb-4">Create Author</h1>
            <AuthorForm :author="null" />
        </div>
    </AppLayout>
</template>
