<template>
  <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
    <tr>
      <th
        v-for="(header, index) in headers"
        :key="header"
        class="px-4 py-3 cursor-pointer select-none hover:text-indigo-600"
        @click="handleSort(fields[index])"
      >
        {{ header }}
        <span v-if="sortBy === fields[index]">
          {{ sortDirection === 'asc' ? '▲' : '▼' }}
        </span>
      </th>
    </tr>
  </thead>
</template>

<script setup>
defineProps({
  headers: Array,
  fields: Array,
  sortBy: String,
  sortDirection: String,
  handleSort: Function,
});
</script>
