import { ref, computed, isRef } from 'vue';

export default function useSortable(rows) {
  const sortBy = ref('');
  const sortDirection = ref('asc');

  const data = isRef(rows) ? rows : ref(rows);

  const handleSort = (field) => {
    if (sortBy.value === field) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortBy.value = field;
      sortDirection.value = 'asc';
    }
  };

  const sortedRows = computed(() => {
    if (!sortBy.value) return data.value;

    return [...data.value].sort((a, b) => {
      const valueA = (a[sortBy.value] ?? '').toString().toLowerCase();
      const valueB = (b[sortBy.value] ?? '').toString().toLowerCase();

      if (valueA < valueB) return sortDirection.value === 'asc' ? -1 : 1;
      if (valueA > valueB) return sortDirection.value === 'asc' ? 1 : -1;
      return 0;
    });
  });

  return { sortBy, sortDirection, handleSort, sortedRows };
}
