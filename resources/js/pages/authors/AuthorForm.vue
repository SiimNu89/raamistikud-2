<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { ref, type PropType } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    author: Object as PropType<Record<string, any> | null>,
});

const form = ref({
    first_name: props.author?.first_name || '',
    last_name: props.author?.last_name || '',
    birth_date: props.author?.birth_date || '',
});

function submit() {
    if (props.author) {
        router.put(`/authors/${props.author.id}`, form.value);
    } else {
        router.post('/authors', form.value);
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4 max-w-md">
        <div>
            <label class="block mb-1">First Name</label>
            <input v-model="form.first_name" class="input" required />
        </div>

        <div>
            <label class="block mb-1">Last Name</label>
            <input v-model="form.last_name" class="input" required />
        </div>

        <div>
            <label class="block mb-1">Birth Date</label>
            <input type="date" v-model="form.birth_date" class="input" />
        </div>

        <Button type="submit">{{ props.author ? 'Update' : 'Create' }}</Button>
    </form>
</template>
