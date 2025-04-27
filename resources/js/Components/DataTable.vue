<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-700">
      <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
        <tr>
          <th v-for="header in headers" :key="header" class="px-4 py-3">
            {{ header }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id" class="border-b hover:bg-gray-50">
          <td v-for="field in fields" :key="field" class="px-4 py-3">
            <component :is="row[field]" v-if="typeof row[field] !== 'string' && typeof row[field] !== 'number'" />
            <span v-else v-html="row[field] ?? '-'"></span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  headers: Array, 
  fields: Array,
  rows: Array,
})
</script>
