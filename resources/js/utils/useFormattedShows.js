import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export default function useFormattedShows() {
  const shows = usePage().props.shows ?? []
  const representations = usePage().props.representations ?? []

  const formattedShows = computed(() => {
    return shows.map(show => {
      const reps = representations
        .filter(rep => rep.show_id === show.id)
        .map(rep => ({
          ...rep,
          location: rep.location || null
        }))

      return {
        id: show.id,
        title: show.title,
        description: show.description,
        duration: show.duration ? `${show.duration} min` : '-',
        bookable: show.bookable ? 'Oui' : 'Non',
        representations: reps
      }
    })
  })

  return { formattedShows }
}
