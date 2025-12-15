jQuery(document).ready(function ($) {
  var ajaxurl = "/wp-admin/admin-ajax.php";

  // Add all selected to cart
  $("#add-selected-to-cart").on("click", function () {
    let items = [];

    $("#lesson-shop-wrapper .quantity").each(function () {
      let input = $(this).find("input.custom-qty");
      let qty = parseInt(input.val());
      let product_id = input.attr("name").replace("qty_", "");

      if (qty > 0) {
        items.push({
          product_id: product_id,
          quantity: qty,
        });
      }
    });

    if (items.length === 0) {
      alert("Please select at least one product.");
      return;
    }

    $.ajax({
      url: ajaxurl,
      method: "POST",
      data: {
        action: "add_multiple_to_cart",
        items: items,
      },
      success: function (response) {
        if (response.success) {
          // alert("Added to cart!");
          location.reload();
        } else {
          alert("Error adding products.");
        }
      },
    });
  });

  // Custom + button
  $(document).on("click", ".custom-plus", function () {
    let input = $(this).siblings(".custom-qty");
    let val = parseInt(input.val()) || 0;
    let max = parseInt(input.attr("max"));

    if (!max || val < max) {
      input.val(val + 1).trigger("change");
    }
  });

  // Custom - button
  $(document).on("click", ".custom-minus", function () {
    let input = $(this).siblings(".custom-qty");
    let val = parseInt(input.val()) || 0;
    let min = parseInt(input.attr("min")) || 0;

    if (val > min) {
      input.val(val - 1).trigger("change");
    }
  });
});
