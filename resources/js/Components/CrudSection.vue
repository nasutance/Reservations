<template>
  <section class="p-6 max-w-screen-xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">{{ title }}</h2>

    <div class="mb-4">
      <button @click="addItem" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">
        + Ajouter
      </button>
    </div>
  <div class="flex justify-center">
    <table class="min-w-[1100px] w-full border rounded shadow text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th v-for="col in columns" :key="col.key" class="border px-4 py-2 text-left font-medium">
            {{ col.label }}
          </th>
          <th class="border px-4 py-2 text-left font-medium">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="item.id ?? index" class="hover:bg-gray-50">
          <td v-for="col in columns" :key="col.key" class="border px-4 py-2 align-top whitespace-nowrap">
            <div v-if="col.editable && isEditing(item)">
              <div v-if="col.type === 'checkboxes'">
                <label v-for="opt in col.options" :key="opt.id" class="block text-sm">
                  <input
                    type="checkbox"
                    :value="opt.id"
                    v-model="items[index][col.bindKey]"
                    class="mr-1"
                  />
                  {{ opt.label }}
                </label>
              </div>
              <input
                v-else
                v-model="items[index][col.key]"
                @blur="markDirty(index)"
                class="border border-gray-300 rounded px-2 py-1 w-full"
              />
            </div>
            <div v-else>
              <template v-if="col.type === 'checkboxes'">
                {{ renderSelectedLabels(item[col.bindKey], col.options) }}
              </template>
              <template v-else>
                <slot :name="col.key" :row="item">
                  {{ formatDisplay(item[col.key], col.key) }}
                </slot>
              </template>
            </div>
          </td>
          <td class="border px-4 py-2 align-top whitespace-nowrap">
            <div v-if="!isEditing(item)" class="flex gap-2">
              <button class="text-blue-600 hover:underline" @click="startEdit(item)">✏️ Modifier</button>
              <button v-if="item.id" class="text-red-600 hover:underline" @click="deleteItem(index)">🗑️ Supprimer</button>
            </div>
            <div v-else class="flex gap-2">
              <button class="text-green-600 hover:underline" @click="saveItem(index)">💾 Enregistrer</button>
              <button class="text-gray-600 hover:underline" @click="cancelEdit(item)">🔄 Annuler</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  </section>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  title: String,
  columns: Array,
  resource: String,
  defaultItem: Object,
  formatItem: Function,
  extraActions: Object,
  relations: Object
});

const items = ref([]);
const editingIds = ref(new Set());
const originalState = ref({});

async function fetchItems() {
  try {
    const response = await axios.get(`/${props.resource}`);
    items.value = props.formatItem
      ? response.data.map(props.formatItem)
      : response.data;
  } catch (err) {
    console.error(err);
  }
}

function addItem() {
  const newItem = { ...props.defaultItem };
  items.value.unshift(newItem);
  startEdit(newItem);
  if (props.extraActions?.afterAdd) props.extraActions.afterAdd(newItem);
}

function saveItem(index) {
  const item = items.value[index];
  const isNew = !item.id;
  const route = `/${props.resource}${isNew ? '' : '/' + item.id}`;
  const method = isNew ? 'post' : 'put';

  router[method](route, item, {
    preserveScroll: true,
    onSuccess: () => {
      editingIds.value.delete(item.id);
      fetchItems();
    },
    onError: (err) => console.error('Save error', err),
  });
}

function deleteItem(index) {
  const item = items.value[index];
  if (!item.id) {
    items.value.splice(index, 1);
    return;
  }

  if (!confirm('Supprimer cet élément ?')) return;

  router.delete(`/${props.resource}/${item.id}`, {
    preserveScroll: true,
    onSuccess: () => fetchItems(),
  });
}

function formatDisplay(value, key) {
  if (props.relations && props.relations[key]) {
    const rel = props.relations[key].find(r => r.id === value);
    return rel ? rel.label : value;
  }
  return value;
}

function renderSelectedLabels(ids, options) {
  if (!Array.isArray(ids)) return '';
  return options
    .filter(opt => ids.includes(opt.id))
    .map(opt => opt.label)
    .join(', ');
}

function startEdit(item) {
  editingIds.value.add(item.id ?? item);
  originalState.value[item.id ?? item] = { ...item };
}

function cancelEdit(item) {
  const id = item.id ?? item;
  const original = originalState.value[id];
  if (original) {
    const index = items.value.findIndex(i => (i.id ?? i) === id);
    items.value[index] = { ...original };
  }
  editingIds.value.delete(id);
}

function isEditing(item) {
  return editingIds.value.has(item.id ?? item);
}

function markDirty(index) {
  // placeholder if needed
}

onMounted(fetchItems);
</script>

<style scoped>
th, td {
  text-align: left;
}
</style>