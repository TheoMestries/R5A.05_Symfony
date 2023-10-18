$(document).ready(function() {
  $('.edit-response-btn').click(function() {
    const responseId = $(this).data('response-id');
    let responseContent = $(`#response-content-${responseId}`)[0].innerText;

    const textarea = `<textarea id="edit-response-textarea-${responseId}" class="form-control">${responseContent}</textarea>`;
    const saveButton = `<button class="btn btn-primary mt-2" onclick="saveResponse(${responseId})">Save</button>`;

    $(`#response-content-${responseId}`).html(textarea + saveButton);

    // Ajustez la hauteur de la textarea
    adjustTextareaHeight(document.getElementById(`edit-response-textarea-${responseId}`));
  });
  $('.delete-response-btn').click(function() {
    const responseId = $(this).data('response-id');
    if (confirm('Êtes-vous sûr de vouloir supprimer cette réponse?')) {
      deleteResponse(responseId);
    }
  });
});

function saveResponse(responseId) {
  const updatedContent = $(`#edit-response-textarea-${responseId}`).val();
  const formattedContent = updatedContent.replace(/\n/g, '<br>'); // Convertissez \n en <br> pour l'affichage

  $.ajax({
    url: `/response/${responseId}/edit`,
    method: 'POST',
    data: {
      content: updatedContent // Envoyez le contenu sans les <br>
    },
    success: function(data) {
      $(`#response-content-${responseId}`).html(formattedContent);
      alert(data.message);
    },
    error: function(jqXHR) {
      alert(jqXHR.responseJSON.error);
    }
  });
}
function deleteResponse(responseId) {
  $.ajax({
    url: `/response/${responseId}/delete`,
    method: 'POST',
    success: function(data) {
      $(`#response-content-${responseId}`).parent().parent().remove(); // Supprime la carte de réponse
      alert(data.message);
    },
    error: function(jqXHR) {
      alert(jqXHR.responseJSON.error);
    }
  });
}


function adjustTextareaHeight(textarea) {
  textarea.style.height = "auto";
  textarea.style.height = (textarea.scrollHeight) + "px";
}
