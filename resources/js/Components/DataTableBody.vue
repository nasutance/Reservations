<template>
  <tbody>
    <tr v-for="row in rows" :key="row.id ?? row.fullname" class="border-b hover:bg-gray-50">
      <td
        v-for="field in fields"
        :key="field"
        class="px-4 py-3"
        :class="field === 'description' ? 'max-w-[400px] whitespace-normal' : ''"
      >
        <slot :name="field" :row="row">
          <span v-html="row[field] ?? '-'"></span>
        </slot>
      </td>
    </tr>
  </tbody>
</template>

<script setup>
defineProps({
  rows: Array,
  fields: Array,
});
</script>
