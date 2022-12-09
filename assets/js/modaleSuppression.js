document.querySelectorAll('.confirmer').forEach(e => e.onclick = deleteModale);

/**
 * Affichage d'une fenêtre modale
 */
function deleteModale() {
  let locationHref = () => {location.href = this.dataset.href};
  let annuler      = () => {document.getElementById('modaleSuppression').close()}; 
  document.querySelector('#modaleSuppression .OK').onclick = locationHref;
  document.querySelector('#modaleSuppression .KO').onclick = annuler;
  document.getElementById('modaleSuppression').showModal();
  document.querySelector('#modaleSuppression .focus').focus();
}