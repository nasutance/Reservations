import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useFormattedArtists() {
  const artists = usePage().props.artists ?? [];

  const formattedArtists = computed(() => {
    return artists.map(artist => {
      const artistTypes = artist.artistTypes ?? artist.artist_types ?? [];

      // Liste de tous ses types globaux
      const allTypes = artistTypes.map(at => at.type?.type).filter(Boolean);

      // Mapping shows -> [types joués dans ce show]
      const showsMap = {};

      for (const artistType of artistTypes) {
        const typeName = artistType.type?.type ?? '-';

        for (const show of artistType.shows ?? []) {
          if (!showsMap[show.title]) {
            showsMap[show.title] = [];
          }
          showsMap[show.title].push(typeName);
        }
      }

      const showsList = Object.entries(showsMap).map(([showTitle, types]) => {
        return `${showTitle} (${types.join(', ')})`;
      });

      return {
        fullname: `${artist.firstname} ${artist.lastname}`,
        typesText: allTypes.join(', '),
        showsText: showsList.join(' / ')
      };
    });
  });

  return { formattedArtists };
}
