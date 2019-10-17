var searchRequest = null;

$(function () {
  var minlength = 0;

  function searchPolitician(value) {
    var value = (typeof value != "string") ? $(this).val() : value;
    var reset = false;
    if (value.length >= minlength ) {
      var url = new URL(window.location.href);
      var page = url.searchParams.get("page");
      if (value.length === 0) reset = true;
      if (searchRequest != null)
        searchRequest.abort();
      searchRequest = $.ajax({
        type: "GET",
        url: "app/backend-logic.php",
        data: {
            'search_keyword' : value,
            'page' : page,
            'reset' : reset
        },
        dataType: "text",
        success: function(response) {
          response = JSON.parse(response);

          $('#politicians-list').html(response.politicians);

          var paginationHtml = response.prevlink +
            " Page " +
            response.page +
            " of " +
            response.pages +
            " pages " +
            response.nextlink;
          $('#pagination').html(paginationHtml);
        }
      });
    }
  }
  $("#search-bar input").keyup(searchPolitician);

  if ($("#search-bar input").val() != "") searchPolitician($("#search-bar input").val());
});