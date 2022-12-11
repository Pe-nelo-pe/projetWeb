document.querySelectorAll('.bid').forEach(e => e.onclick = bidModale);

/**
 * Affichage d'une fenêtre modale
 */
function bidModale() {
  let locationHref = () => {location.href = this.dataset.href};
  let annuler      = () => {document.getElementById('modaleBid').close()}; 
  document.querySelector('#modaleBid .OK').onclick = locationHref;
  document.querySelector('#modaleBid .KO').onclick = annuler;
  document.getElementById('modaleBid').showModal();
  document.querySelector('#modaleBid .focus').focus();
}