import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useFormattedShows() {
  const shows = usePage().props.shows ?? [];

  const formattedShows = computed(() => {
    return shows.map(show => ({
      id: show.id,
      title: show.title,
      description: show.description,
      duration: show.duration ? `${show.duration} min` : '-',
      bookable: show.bookable ? 'Oui' : 'Non',
      representations: show.representations ?? [], // On garde brut ici
    }));
  });

  return { formattedShows };
}
