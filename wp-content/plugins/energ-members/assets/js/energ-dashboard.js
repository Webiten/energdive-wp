jQuery(function ($) {

    // --------------------------
    // AUTO READ TRACKING
    // --------------------------

    // Find post ID (PHP prints ENERG_POST_ID in single templates)
    const postId =
        (typeof ENERG_POST_ID !== "undefined")
            ? ENERG_POST_ID
            : (document.body.dataset.postId || null);

    if (postId) {
        if (typeof ENERG_AJAX === "undefined") {
            console.error("ENERG_AJAX missing! Tracking cannot run.");
            return;
        }

        console.log("📌 Tracking read for post:", postId);

        $.post(ENERG_AJAX.ajax_url, {
            action: "energ_track_read",
            nonce: ENERG_AJAX.nonce,
            post_id: postId
        })
        .done(function (res) {
            console.log("📌 READ TRACK RESPONSE:", res);
        })
        .fail(function (xhr) {
            console.warn("❌ energ_track_read failed");
            console.log(xhr.responseText);
        });
    } else {
        console.warn("⚠ No post ID found — skipping read tracking.");
    }

});


/* --------------------------
   FAVORITES TOGGLE HANDLER
-------------------------- */
jQuery(function ($) {

    $(document).on("click", ".energ-fav-btn", function (e) {
        e.preventDefault();

        const postId = $(this).data("post-id");
        if (!postId) return;

        if (typeof ENERG_AJAX === "undefined") {
            console.error("ENERG_AJAX missing! Cannot toggle favorite.");
            return;
        }

        $.post(ENERG_AJAX.ajax_url, {
            action: "energ_toggle_fav",
            nonce: ENERG_AJAX.nonce,
            post_id: postId
        })
        .done(function (res) {
            if (res.success) {
                // update all fav buttons for this post
                $('.energ-fav-btn[data-post-id="' + postId + '"]').toggleClass("is-fav");
            } else {
                alert(res.data || "Favorite update failed");
            }
        })
        .fail(function () {
            console.warn("❌ energ_toggle_fav AJAX error");
        });
    });

});
