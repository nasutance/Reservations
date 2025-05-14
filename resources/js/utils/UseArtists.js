// composables/useArtists.js
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

export function useArtists() {
  const page = usePage()
  const types = ref(page.props.types ?? [])
  const shows = ref(page.props.shows ?? [])
  const artistTypes = ref(page.props.artistTypes ?? [])
  const localArtists = ref([])
  const editingIds = ref(new Set())

  // Watchers à appeler dans le composant
  const hydrateLocalArtists = () => {
    const rawArtists = page.props.artists ?? []
    const rawArtistTypes = page.props.artistTypes ?? []

    localArtists.value = rawArtists.map(artist => {
      const related = rawArtistTypes.filter(at => at.artist_id === artist.id)
      const typeIds = related.map(at => at.type_id)
      const showMap = Object.fromEntries(
        related.map(at => [at.type_id, at.shows?.map(s => s.id) ?? []])
      )

      const showsTextMap = {}
      for (const at of related) {
        const type = at.type?.type
        for (const show of at.shows ?? []) {
          if (!showsTextMap[show.title]) showsTextMap[show.title] = []
          showsTextMap[show.title].push(type)
        }
      }

      const showsText = Object.entries(showsTextMap)
        .map(([title, tps]) => `${title} (${tps.join(', ')})`)
        .join(' / ')

      return {
        id: artist.id,
        firstname: artist.firstname,
        lastname: artist.lastname,
        selectedTypeIds: typeIds,
        selectedShowTypeMap: showMap,
        showsText
      }
    })
  }

  const isEditing = id => editingIds.value.has(id)

  const toggleEdit = id => {
    if (isEditing(id)) editingIds.value.delete(id)
    else editingIds.value.add(id)
  }

  const addNewArtistRow = () => {
    const newRow = {
      id: `new-${Date.now()}-${Math.random().toString(36).slice(2, 6)}`,
      firstname: '',
      lastname: '',
      selectedTypeIds: [],
      selectedShowTypeMap: {},
      isNew: true
    }
    localArtists.value.unshift(newRow)
    editingIds.value.add(newRow.id)
  }

  const getTypeLabel = id => types.value.find(t => t.id === id)?.type ?? `Type ${id}`
  const getTypeLabels = ids => ids.map(getTypeLabel)

  const getShowBinding = (row, typeId) => computed({
    get() {
      if (!Array.isArray(row.selectedShowTypeMap[typeId])) {
        row.selectedShowTypeMap[typeId] = []
      }
      return row.selectedShowTypeMap[typeId]
    },
    set(newValue) {
      row.selectedShowTypeMap[typeId] = newValue
    }
  })

  const saveArtist = async (row) => {
    const method = row.isNew ? 'post' : 'put'
    const url = row.isNew ? '/artist' : `/artist/${row.id}`

    for (const typeId of row.selectedTypeIds) {
      if (!Array.isArray(row.selectedShowTypeMap[typeId])) {
        row.selectedShowTypeMap[typeId] = []
      }
    }

    await router[method](url, {
      firstname: row.firstname,
      lastname: row.lastname,
      types: row.selectedTypeIds,
      shows: row.selectedShowTypeMap,
    }, {
      onSuccess: () => {
        editingIds.value.delete(row.id)
        router.reload({ preserveScroll: true })
      }
    })
  }

  const deleteArtist = id => {
    if (!confirm('Supprimer définitivement cet artiste ?')) return
    router.delete(`/artist/${id}`, {
      preserveScroll: true,
      onSuccess: () => {
        editingIds.value.delete(id)
        router.reload({ preserveScroll: true })
      }
    })
  }

  return {
    types,
    shows,
    localArtists,
    editingIds,
    isEditing,
    toggleEdit,
    addNewArtistRow,
    getTypeLabel,
    getTypeLabels,
    getShowBinding,
    saveArtist,
    deleteArtist,
    hydrateLocalArtists
  }
}
