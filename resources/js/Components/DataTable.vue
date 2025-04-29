<template>
  <div class="w-full flex justify-center">
    <table class="min-w-[1300px] text-sm text-left text-gray-700">
      <DataTableHead
        :headers="headers"
        :fields="fields"
        :sortBy="sortBy"
        :sortDirection="sortDirection"
        :handleSort="handleSort"
      />
      <DataTableBody :rows="sortedRows" :fields="fields">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
          <slot :name="name" v-bind="slotProps" />
        </template>
      </DataTableBody>
    </table>
  </div>
</template>

<script setup>
import useSortable from '@/utils/useSortable';
import DataTableHead from '@/Components/DataTableHead.vue';
import DataTableBody from '@/Components/DataTableBody.vue';

const props = defineProps({
  headers: Array,
  fields: Array,
  rows: Array,
});

const { sortBy, sortDirection, handleSort, sortedRows } = useSortable(props.rows);
</script>
