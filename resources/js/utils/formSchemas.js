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
   price: [
    { name: 'type', label: 'Libellé', type: 'text' },
    { name: 'description', label: 'Description', type: 'text' },
    { name: 'price', label: 'Montant', type: 'number' },
    { name: 'start_date', label: 'Début', type: 'date' },
    { name: 'end_date', label: 'Fin', type: 'date' },
  ],
}
