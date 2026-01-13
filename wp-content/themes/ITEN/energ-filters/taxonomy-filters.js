jQuery(document).ready(function($) {
    console.log("taxonomy-filters.js loaded ");

    let lastChanged = null;

    function fetchFilteredPosts() {
        let data = {
            action: 'filter_by_taxonomies',
            sector: $('#filter-sector').val(),
            content_type: $('#filter-type').val(),
            tags: $('#filter-tags').val(),
            worlds: $('#filter-world').val(),
            states: $('#filter-states').val(),
            author: $('#filter-author').val(),
            from: $('#filter-from').val(),
            to: $('#filter-to').val(),
        };

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function() {
                $('.filtered-results').html('<p>Loading...</p>').show();
                $('.default-results').hide();
            },
            success: function(response) {
                $('.filtered-results').html(response);
                updateFilterStates(); // refresh available filter options dynamically
            }
        });
    }

    //Dynamically update available options based on results
    function updateFilterStates() {
        let data = {
            action: 'energ_dynamic_filter_update',
            sector: $('#filter-sector').val(),
            content_type: $('#filter-type').val(),
            tags: $('#filter-tags').val(),
            worlds: $('#filter-world').val(),
            states: $('#filter-states').val(),
        };

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: data,
            success: function(res) {
                if (res.success) {
                    let available = res.data;
                    console.log("Available terms:", available);

                    const allTaxonomies = ['sector', 'content_type', 'tags', 'worlds', 'states'];

                    allTaxonomies.forEach(tax => {
                        // Skip disabling the filter the user just interacted with
                        if (tax === lastChanged) return;

                        let $select = $('#filter-' + tax.replace('_', '-'));
                        $select.find('option').each(function() {
                            let val = $(this).val();
                            if (val && (!available[tax] || !available[tax][val])) {
                                $(this).prop('disabled', true).css({
                                    color: '#777',
                                    background: '#222',
                                    cursor: 'not-allowed'
                                });
                            } else {
                                $(this).prop('disabled', false).css({
                                    color: '#fff',
                                    background: '#333',
                                    cursor: 'pointer'
                                });
                            }
                        });
                    });
                }
            }
        });
    }

    //Track which filter was changed
    $(document).on('change', '.taxonomy-filters select, .taxonomy-filters input', function() {
        lastChanged = $(this).attr('id').replace('filter-', '').replace('-', '_');
        fetchFilteredPosts();
    });

    //Clear All button
    $(document).on('click', '#clear-filters', function() {
        $('.taxonomy-filters select, .taxonomy-filters input').val('');
        $('.filtered-results').hide();
        $('.default-results').fadeIn(300);
        lastChanged = null;
        updateFilterStates();
    });

    //Initial setup
    updateFilterStates();
});
