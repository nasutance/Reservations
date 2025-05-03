import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export default function useFormattedArtists() {
  const artists = usePage().props.artists ?? []
  const artistTypes = usePage().props.artistTypes ?? []

  const formattedArtists = computed(() => {
    return artists.map(artist => {
      const relatedArtistTypes = artistTypes.filter(at => at.artist_id === artist.id)

      const allTypes = relatedArtistTypes.map(at => at.type?.type).filter(Boolean)

      const showsMap = {}

      for (const artistType of relatedArtistTypes) {
        const typeName = artistType.type?.type ?? '-'

        for (const show of artistType.shows ?? []) {
          if (!showsMap[show.title]) {
            showsMap[show.title] = []
          }
          showsMap[show.title].push(typeName)
        }
      }

      const showsList = Object.entries(showsMap).map(([showTitle, types]) => {
        return `${showTitle} (${types.join(', ')})`
      })

      return {
  id: artist.id,
  firstname: artist.firstname,
  lastname: artist.lastname,
  fullname: `${artist.firstname} ${artist.lastname}`,
  typesText: allTypes.join(', '),
  showsText: showsList.join(' / '),

  // Ajouté :
  selectedTypeIds: relatedArtistTypes.map(at => at.type_id),
  selectedShowTypeMap: Object.fromEntries(
    relatedArtistTypes.map(at => [
      at.type_id,
      at.shows.map(show => show.id)
    ])
  )
}

    })
  })

  return { formattedArtists }
}
