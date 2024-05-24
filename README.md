# Awesome Footnotes

<svg fill="currentColor" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 512.001 512.001" xml:space="preserve" width="120" align="left" style="margin:25px; float:left">
    <g>
        <g>
            <path d="M510.674,193.267l-75.466-130.71c-2.735-4.734-8.788-6.359-13.525-3.624l-78.83,45.513V17.381
                c0-5.467-4.434-9.901-9.901-9.901H182.019c-5.467,0-9.901,4.434-9.901,9.901v90.494l-81.8-47.227
                c-4.738-2.734-10.79-1.112-13.525,3.624L1.327,194.982c-1.313,2.274-1.669,4.976-0.989,7.513c0.679,2.537,2.339,4.699,4.613,6.012
                l78.831,45.513l-75.4,43.533c-4.736,2.735-6.359,8.789-3.624,13.525l75.465,130.71c2.735,4.736,8.79,6.36,13.525,3.624
                l78.37-45.247v94.455c0,5.467,4.434,9.901,9.901,9.901h150.932c5.469,0,9.901-4.434,9.902-9.901v-91.026l75.4,43.533
                c2.273,1.313,4.975,1.67,7.513,0.989c2.537-0.679,4.699-2.339,6.012-4.613l75.465-130.71c2.735-4.736,1.112-10.79-3.624-13.525
                l-78.37-45.248l81.801-47.228c2.274-1.313,3.933-3.474,4.613-6.012C512.344,198.244,511.987,195.542,510.674,193.267z
                M400.496,245.447c-3.063,1.768-4.951,5.038-4.951,8.574c0,3.537,1.887,6.806,4.951,8.575l84.648,48.872l-65.564,113.56
                l-81.677-47.157c-3.063-1.769-6.838-1.769-9.901,0c-3.063,1.768-4.951,5.038-4.951,8.575v98.274h-131.13V383.016
                c0-3.537-1.887-6.806-4.951-8.574c-3.063-1.769-6.838-1.769-9.901,0l-84.648,48.871l-65.564-113.56l81.677-47.157
                c3.063-1.768,4.951-5.038,4.951-8.574c0-3.537-1.887-6.806-4.951-8.574l-85.108-49.137L88.991,82.748l88.078,50.852
                c3.063,1.769,6.838,1.769,9.901,0c3.063-1.768,4.951-5.038,4.951-8.575V27.282h131.13v94.313c0,3.537,1.887,6.806,4.951,8.574
                c3.063,1.769,6.838,1.769,9.901,0l85.107-49.137l65.565,113.562L400.496,245.447z"/>
        </g>
    </g>
    <g>
        <g>
            <path d="M213.92,76.788c-5.467,0-9.901,4.434-9.901,9.901v46.536c0,5.467,4.434,9.901,9.901,9.901s9.901-4.434,9.901-9.901V86.689
                C223.821,81.222,219.388,76.788,213.92,76.788z"/>
        </g>
    </g>
    <g>
        <g>
            <path d="M213.92,40.153c-5.467,0-9.901,4.434-9.901,9.901v5.941c0,5.467,4.434,9.901,9.901,9.901s9.901-4.434,9.901-9.901v-5.941
                C223.821,44.587,219.388,40.153,213.92,40.153z"/>
        </g>
    </g>
</svg>Awesome Footnotes is a simple, but powerful, method of adding footnotes into your posts and pages.

Features include...

* Simple footnote insertion via double parentheses
* Combine identical notes
* Solution for paginated posts
* Suppress Footnotes on specific page types
* Option to display ‘pretty’ tooltips using jQuery
* Lots of configuration options

Creating a footnote is incredibly simple - you just need to include your footnote in double parentheses, such as this...

> This is a sentence ((and this is your footnote)).

The footnote will then appear at the bottom of your post/page.

<p align="right"><a href="https://wordpress.org/plugins/footnotes-made-easy/"><img src="https://img.shields.io/wordpress/plugin/dt/footnotes-made-easy?label=wp.org%20downloads&style=for-the-badge"> <img src="https://img.shields.io/wordpress/plugin/stars/footnotes-made-easy?color=orange&style=for-the-badge"></a></p>

## Hooks

```
/**
 * Apply the content filters - parses the content and adds the extracted footnotes
 *
 * @param array - The array with the hooks to be applied for the processing.
 */
\apply_filters( 'awef_process_content_hooks', array( 'the_content' ) );
```
- Called in class-footnotes-formatter.php


```
/**
 * Gives the ability to change the footnotes header to something else
 *
 * @param string - The parsed footnotes header.
 *
 * @since 2.4.0
 */
\apply_filters( 'awef_footnotes_header', $footnotes_header );
```
- Called in class-footnotes-formatter.php

```
/**
 * Gives the ability to change the footnotes header to something else
 *
 * @param string - The parsed footnotes header.
 *
 * @since 2.4.0
 */
\apply_filters( 'awef_footnotes_footer', $footnotes_footer );
```
- Called in class-footnotes-formatter.php
