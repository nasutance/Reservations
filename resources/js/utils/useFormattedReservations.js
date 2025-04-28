import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { formatDate } from '@/utils/index';

export default function useFormattedReservations() {
    const reservations = usePage().props.reservations || [];
    const prices = usePage().props.prices || [];

    const formattedReservations = computed(() => {
        return reservations.map(resa => ({
            id: resa.id,
            user: resa.user ? `${resa.user.firstname} ${resa.user.lastname}` : '-',
            showTitle: resa.representations[0]?.show?.title || '-',
            schedule: resa.representations[0]?.schedule ? formatDate(resa.representations[0].schedule) : '-',
            status: resa.status,
            detail: resa.representations.length
                ? resa.representations
                      .map(rep => {
                          const price = prices.find(p => p.id === rep.pivot.price_id);
                          return price ? `${rep.pivot.quantity} ${price.description}` : `${rep.pivot.quantity} -`;
                      })
                      .join('<br>')
                : '-',
        }));
    });

    return { formattedReservations };
}
