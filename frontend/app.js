$(function() {
  var rootURL = 'wines';
  window.Wine = {
    findAll: function() {
      $.ajax({
        type: 'GET',
        url: rootURL,
        dataType: "json", // data type of response
        //success: renderList
        success: function(data) {
          console.log(data);
        }
      });
    },
    findByName: function(searchKey) {
      $.ajax({
        type: 'GET',
        url: rootURL + '/search/' + searchKey,
        dataType: "json",
        //success: renderList
        success: function(data) {
          console.log(data);
        }
      });
    },
    findById: function(id) {
      $.ajax({
        type: 'GET',
        url: rootURL + '/' + id,
        dataType: "json",
        success: function(data) {
          console.log(data);
          //$('#btnDelete').show();
          //renderDetails(data);
        }
      });
    },

    addWine: function() {
      console.log('addWine');
      $.ajax({
        type: 'POST',
        contentType: 'application/json',
        url: rootURL,
        dataType: "json",
        data: formToJSON(),
        success: function(data, textStatus, jqXHR) {
          alert('Wine created successfully');
          //$('#btnDelete').show();
          //$('#wineId').val(data.id);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('addWine error: ' + textStatus);
        }
      });
    },
    updateWine: function() {
      $.ajax({
        type: 'PUT',
        contentType: 'application/json',
        url: rootURL + '/' + $('#wineId').val(),
        dataType: "json",
        data: formToJSON(),
        success: function(data, textStatus, jqXHR) {
          alert('Wine updated successfully');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('updateWine error: ' + textStatus);
        }
      });
    },
    deleteWine: function() {
      console.log('deleteWine');
      $.ajax({
        type: 'DELETE',
        url: rootURL + '/' + $('#wineId').val(),
        success: function(data, textStatus, jqXHR) {
          alert('Wine deleted successfully');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('deleteWine error');
        }
      });
    },
    // Helper function to serialize all the form fields into a JSON string
    formToJSON: function() {
      return JSON.stringify({
        "id": $('#id').val(),
        "name": $('#name').val(),
        "grapes": $('#grapes').val(),
        "country": $('#country').val(),
        "region": $('#region').val(),
        "year": $('#year').val(),
        "description": $('#description').val()
      });
    }
  };
});
