export const formSchemas = {
  reservation: [
    { name: 'representation_id', label: 'Représentation', type: 'select', options: [], required: true },
    { name: 'price_id', label: 'Tarif', type: 'select', options: [], required: true },
    { name: 'quantity', label: 'Quantité', type: 'number', required: true }
  ],
  artist: [
     { name: 'firstname', label: 'Prénom', type: 'text', editOnly: true },
     { name: 'lastname', label: 'Nom', type: 'text', editOnly: true }
   ],
}
