import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useFormattedUsers() { // <= majuscule F
  const users = usePage().props.users ?? [];

  const formattedUsers = computed(() => {
    return users.map(user => ({
      id: user.id,
      firstname: user.firstname,
      lastname: user.lastname,
      email: user.email,
      langue: user.langue,
      role: user.roles.length > 0 ? user.roles[0].role : '-' 
    }));
  });

  return { formattedUsers };
}
