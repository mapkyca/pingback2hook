/**
 * @file
 * 
 * Javascrip Pingback2Hook library.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */
(function($) {

    /**
     * Fetch the latest comments - webmention/pingbacks.
     * @param url host The host to talk to
     * @param text endpoint The endpoint
     * @param url target_url The target url
     * @param offset int Offset
     * @param limit int Number of comments to return
     */
    $.fn.mentions = function(options) {

        /**
         * Borrowing strip_tags.js code from http://phpjs.org/functions/strip_tags/
         * 
         * @param {type} input
         * @param {type} allowed
         * @returns {@exp;input@pro;replace@call;@call;replace|@exp;@exp;input@pro;replace@call;@call;replace}
         */
        function strip_tags(input, allowed) {
            // http://kevin.vanzonneveld.net
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Luke Godfrey
            // +      input by: Pul
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Onno Marsman
            // +      input by: Alex
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: Marc Palau
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Eric Nagel
            // +      input by: Bobby Drake
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Tomasz Wesolowski
            // +      input by: Evertjan Garretsen
            // +    revised by: Rafał Kukawski (http://blog.kukawski.pl/)
            // *     example 1: strip_tags('<p>Kevin</p> <br /><b>van</b> <i>Zonneveld</i>', '<i><b>');
            // *     returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
            // *     example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
            // *     returns 2: '<p>Kevin van Zonneveld</p>'
            // *     example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>", "<a>");
            // *     returns 3: '<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>'
            // *     example 4: strip_tags('1 < 5 5 > 1');
            // *     returns 4: '1 < 5 5 > 1'
            // *     example 5: strip_tags('1 <br/> 1');
            // *     returns 5: '1  1'
            // *     example 6: strip_tags('1 <br/> 1', '<br>');
            // *     returns 6: '1  1'
            // *     example 7: strip_tags('1 <br/> 1', '<br><br/>');
            // *     returns 7: '1 <br/> 1'
            allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
            return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            });
        }

        // Set some defaults
        var defaults = {
            target_url: '',
            offset: 0,
            limit: 10
        };

        // Consolidate defaults
        var options = $.extend(defaults, options);

        return this.each(function() {

            var selObject = $(this);

            if (options.host == "") {
                selObject.html("Host URL (host) not passed to library.");
                return;
            }

            if (options.example == "") {
                selObject.html("Endpoint (endpoint) not passed to library.");
                return;
            }

            if (options.target_url != "")
            {
                // Construct URL
                var endpoint;
                endpoint = options.host + 'api/' + options.endpoint + '/latest.jsonp';

                // Make a call
                $.ajax(endpoint, {
                    dataType: "jsonp",
                    type: "GET",
                    url: endpoint,
                    data: {
                        target_url: options.target_url,
                        offset: options.offset,
                        limit: options.limit
                    },
                    jsonp: 'callback',
                    jsonpCallback: 'jsonpCallback',
                    success: function(data) {

                        var output = "";
                        var item;
                        var value;
                        var details;
                        var date;
                        var mf2;

                        for (i = 0; i < data.rows.length; i++) {

                            item = data.rows[i];
                            value = item.value;
                            details = value.details;
                            date = new Date(value.unix_timestamp * 1000);

                            // Begin
                            output += '<div id="' + item.id + '" class="p2h-item h-cite p-comment "';
                            if (typeof details.handler != 'undefined') {
                                output += details.handler;
                            }
                            output += '">';

                            if (typeof details.mf2 != 'undefined') {
                                // MF2 Content
                                mf2 = details.mf2;

                                var author = "";
                                var home = "";
                                var photo = "";
                                var content = "";
                                var entry;

                                for (ia = 0; ia < mf2.items.length; ia++) {

                                    entry = mf2.items[ia];

                                    if ($.inArray('h-entry', entry.type)) {

                                        if (author == "") {
                                            author = entry.properties.author[0].properties.name[0];
                                        }

                                        if (home == "") {
                                            home = entry.properties.author[0].properties.url[0];
                                        }

                                        if (photo == "") {
                                            photo = entry.properties.author[0].properties.photo[0];
                                        }

                                        if (content == "") {
                                            content = strip_tags(entry.properties.content[0], '<p><br><a>');
                                        }
                                    }

                                }

                                output += '<div class="p2h-author-icon ">';
                                output += '<address class="p-author author vcard h-card">';
                                output += '<img src="' + photo + '" class="u-photo" height="50" width="50" />';
                                output += '<cite class="fn p-name"><a href="' + home + '" rel="external nofollow" class="u-url url">' + author + '</a></cite> ';
                                output += '<span class="says">says:</span>        ';
                                output += '</address>';
                                output += '</div>';
                                output += '<div class="p2h-comment-details">';
                                output += '<p class="comment-meta">';
                                output += '<a href="' + value.source_url + '" rel="nofollow bookmark" class="note-published u-url">';
                                output += '<time class="dt-published published dt-updated updated" datetime="' + date.toISOString() + '">' + date.toLocaleString() + '</time>';
                                output += '</a>';
                                output += '</p>';
                                output += '<p class="p-summary">' + content + '</p>';
                                output += '</div>';

                            } else {
                                // Regular content
                                output += '<p>';
                                output += '<a href="' + value.source_url + '" rel="nofollow bookmark" class="note-published u-url">' + details.title + '</a> mentioned <a class="u-url u-in-reply-to" href="' + value.target_url + '">this</a> on ';
                                output += '<time class="dt-published published dt-updated updated" datetime="' + date.toISOString() + '">' + date.toLocaleString() + '</time>';
                                output += '</p>';

                            }

                            // End
                            output += '</div>';
                        }

                        selObject.html(output);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        selObject.html(jqXHR.responseJSON.error_description);
                    }

                });
            }
            else
            {
                selObject.html("Target URL (target_url) not passed to library.");
            }

        });
    }

}(jQuery));
