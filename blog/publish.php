<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Increase maximum execution time and memory limit if needed
ini_set('max_execution_time', '300');
ini_set('memory_limit', '512M');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Handle deletion if tempId is passed
    if (isset($_POST['tempId'])) {
        $tempId = $_POST['tempId'];
        $tempFilePath = __DIR__ . '/temp.json';

        if (file_exists($tempFilePath)) {
            $tempData = json_decode(file_get_contents($tempFilePath), true);

            if (isset($tempData[$tempId])) {
                // Delete related data using temp data
                $slug = $tempData[$tempId]['slug'];

                // Delete the HTML file
                $postFileName = __DIR__ . '/' . $slug . '.html';
                if (file_exists($postFileName)) {
                    unlink($postFileName);
                }

                // Delete the featured image
                $featuredImagePath = str_replace('https://incshipping.com/blog/', __DIR__ . '/', $tempData[$tempId]['featuredImage']);
                if (file_exists($featuredImagePath)) {
                    unlink($featuredImagePath);
                }

                // Delete from timestamp.json
                $timestampFilePath = __DIR__ . '/timestamp.json';
                if (file_exists($timestampFilePath)) {
                    $timestampData = json_decode(file_get_contents($timestampFilePath), true);
                    foreach ($timestampData as $timestamp => $data) {
                        if ($data['slug'] === $slug) {
                            unset($timestampData[$timestamp]);
                            file_put_contents($timestampFilePath, json_encode($timestampData, JSON_PRETTY_PRINT));
                            break;
                        }
                    }
                }

                // Delete from tags.json
                $tagsFilePath = __DIR__ . '/tags.json';
                if (file_exists($tagsFilePath)) {
                    $tagsData = json_decode(file_get_contents($tagsFilePath), true);
                    foreach ($tagsData['hashtags'] as $tag => $posts) {
                        if (isset($posts[$slug . '.html'])) {
                            unset($tagsData['hashtags'][$tag][$slug . '.html']);
                            if (empty($tagsData['hashtags'][$tag])) {
                                unset($tagsData['hashtags'][$tag]);
                            }
                        }
                    }
                    file_put_contents($tagsFilePath, json_encode($tagsData, JSON_PRETTY_PRINT));
                }

                // Remove temp data after successful deletion
                unset($tempData[$tempId]);
                file_put_contents($tempFilePath, json_encode($tempData, JSON_PRETTY_PRINT));
            }
        }
    }

    // Ensure all form fields are present
    $required_fields = ['title', 'content', 'focusKeyphrase', 'seoTitle', 'slug', 'metaDescription', 'tags', 'visibility', 'category'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            die("Error: Missing $field");
        }
    }

    // Get form data
    $title = htmlspecialchars($_POST['title']);
    $content = $_POST['content'];
    $focusKeyphrase = htmlspecialchars($_POST['focusKeyphrase']);
    $seoTitle = htmlspecialchars($_POST['seoTitle']);
    $slug = htmlspecialchars($_POST['slug']);
    $metaDescription = htmlspecialchars($_POST['metaDescription']);
    $canonicalUrl = isset($_POST['canonicalUrl']) && !empty($_POST['canonicalUrl']) ? htmlspecialchars($_POST['canonicalUrl']) : $rootPath . $slug ;
    $headScripts = $_POST['headSrcipts'];
    $bodyScripts = $_POST['bodySrcipts'];
    $structuredDataInput = $_POST['structuredData'];
    $tags = $_POST['tags'];
    $visibility = $_POST['visibility'];
    $category = htmlspecialchars($_POST['category']); // New category field
    // New geo-location fields
    $geoRegion = htmlspecialchars($_POST['geoRegion']);
    $geoPlacename = htmlspecialchars($_POST['geoPlacename']);
    $geoPosition = htmlspecialchars($_POST['geoPosition']);
    $ICBM = htmlspecialchars($_POST['ICBM']);

    // Extract the first line from the content
    $plainTextContent = strip_tags($content);
    $firstLine = substr($plainTextContent, 0, 100);

    // Handle image upload
    $targetDir = "uploads/";
    $featuredImage = "";

    // Check if the post is being edited
    $isEditing = isset($_POST['isEditing']) && $_POST['isEditing'] === 'true';

    if (!empty($_FILES['featuredImage']['name'])) {
        // If a new image is uploaded, process the image
        $targetFile = $targetDir . basename($_FILES["featuredImage"]["name"]);
        if (move_uploaded_file($_FILES["featuredImage"]["tmp_name"], $targetFile)) {
            $featuredImage = $targetFile;
        } else {
            echo"<script type='text/javascript'>alert('Invalid request method.');</script>";
            // die("Error: Unable to upload image.");
        }
    } else {
        // If no new image is uploaded and this is an edit, retain the existing image
        if ($isEditing) {
            $timestampFilePath = __DIR__ . '/timestamp.json';
            if (file_exists($timestampFilePath)) {
                $timestampData = json_decode(file_get_contents($timestampFilePath), true);
                foreach ($timestampData as $timestamp => $data) {
                    if ($data['slug'] === $slug) {
                        $featuredImage = str_replace($data['featuredImage']);
                        break;
                    }
                }
            }
        }
    }

    // If the featured image is still empty, ensure it's not accidentally cleared
    if (empty($featuredImage)) {
        $timestampFilePath = __DIR__ . '/timestamp.json';
            if (file_exists($timestampFilePath)) {
                $timestampData = json_decode(file_get_contents($timestampFilePath), true);
                foreach ($timestampData as $timestamp => $data) {
                    if ($data['slug'] === $slug) {
                        $featuredImage = str_replace($rootPath, '', $data['featuredImage']);
                        break;
                    }
                }
            }
    }

    // User-defined global variables
    $domainName = 'https://incshipping.com/';
    $rootPath = 'https://incshipping.com/blog/';
    $language = 'en_US';
    $openGraphType = 'article';
    $publisherUrl = 'https://www.facebook.com/incshipping';
    $publisherName = 'INC Shipping LLC';
    $publisherTwitterId = '@incexpressshipping';
    $publisherLogo = 'https://incshipping.com/img/inc_nav_logo.webp';
    $publisherTagline = 'INC Express Shipping LLC is one of the dedicated logistics, warehousing, and Best Logistics and Shipping Company in Dubai, UAE.';
    $favioconLink = 'https://incshipping.com/img/favicon.jpeg';
    $blogHome = 'https://incshipping.com/blog.html';
    $facebookProfileLink = 'https://www.facebook.com/incshipping';
    $instagramProfileLink = 'https://www.instagram.com/incexpressshipping/';
    $threadsProfileLink = 'https://www.instagram.com/incexpressshipping/';
    $twitterProfileLink = 'https://www.instagram.com/incexpressshipping/';
    $linkedinProfileLink = 'https://www.linkedin.com/company/inc-express-shipping-l-l-c/';
    $whatsappProfileLink = 'https://wa.me/+971503524424';
    $youtubeProfileLink = 'https://www.instagram.com/incexpressshipping/';
    $publisherAddress = 'Office M29, Freight gate 4 Dubai Airport Freezone Dubai, UAE';
    $publisherMobile = '+971503524424';
    $publisherEmail = 'sales@incshipping.com';
    $privacyPolicy = 'https://incshipping.com/privacy-policy.html';
    $termsAndCondition = 'https://incshipping.com/terms-and-condition.html';
    $siteMap = 'https://incshipping.com/sitemap.html';

    // Processed variables
    // $canonicalUrl = $rootPath . $slug . '.html';
    $CurrentDateTime = date('c');
    $featuredImageUrl = $rootPath . $featuredImage;
    $logoImageUrl = $rootPath . $publisherLogo;
    $formattedPublishDate = date('F j, Y');
    $blogHomeUrl = $domainName . $blogHome;
    $privacyPolicyUrl = $domainName . $privacyPolicy;
    $termsAndConditionUrl = $domainName . $termsAndCondition;
    $siteMapUrl = $domainName . $siteMap;
    $categoryLinks = '<a href="categories.html?category=' . urlencode($category) . '">' . htmlspecialchars($category) . '</a>';
    $structuredDataInput = isset($_POST['structuredData']) ? $_POST['structuredData'] : ''; // Check if the field is set
    if (!empty($structuredDataInput)) {
        // If the structuredDataInput is not empty, use the user's input
        $structuredData = $structuredDataInput;
    } else {
        $structuredData = '
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@graph": [
                    {
                        "@type": "$openGraphType",
                        "@id": "$canonicalUrl/#$openGraphType",
                        "isPartOf": {
                            "@id": "$canonicalUrl/"
                        },
                        "author": {
                            "name": "$publisherName",
                            "@id": "$blogHomeUrl"
                        },
                        "headline": "$title",
                        "datePublished": "$CurrentDateTime",
                        "mainEntityOfPage": {
                            "@id": "$canonicalUrl/"
                        },
                        "wordCount": 365,
                        "commentCount": 0,
                        "publisher": {
                            "@id": "$blogHomeUrl"
                        },
                        "image": {
                            "@id": "$canonicalUrl/#primaryimage"
                        },
                        "thumbnailUrl": "$featuredImageUrl",
                        "keywords": [
                            $formattedTagsString
                        ],
                        "articleSection": [
                            "Blog"
                        ],
                        "inLanguage": "$language"
                    },
                    {
                        "@type": "WebPage",
                        "@id": "$canonicalUrl/",
                        "url": "$canonicalUrl/",
                        "name": "$seoTitle",
                        "isPartOf": {
                            "@id": "$blogHomeUrl"
                        },
                        "primaryImageOfPage": {
                            "@id": "$canonicalUrl/#primaryimage"
                        },
                        "image": {
                            "@id": "$canonicalUrl/#primaryimage"
                        },
                        "thumbnailUrl": "$featuredImageUrl",
                        "datePublished": "$CurrentDateTime",
                        "description": "$metaDescription.",
                        "breadcrumb": {
                            "@id": "$canonicalUrl/#breadcrumb"
                        },
                        "inLanguage": "$language",
                        "potentialAction": [
                            {
                                "@type": "ReadAction",
                                "target": [
                                    "$canonicalUrl/"
                                ]
                            }
                        ]
                    },
                    {
                        "@type": "ImageObject",
                        "inLanguage": "$language",
                        "@id": "$canonicalUrl/#primaryimage",
                        "url": "$featuredImageUrl",
                        "contentUrl": "$featuredImageUrl",
                        "caption": "$title"
                    },
                    {
                        "@type": "BreadcrumbList",
                        "@id": "$canonicalUrl/#breadcrumb",
                        "itemListElement": [
                            {
                                "@type": "ListItem",
                                "position": 1,
                                "name": "Home",
                                "item": "$blogHomeUrl"
                            },
                            {
                                "@type": "ListItem",
                                "position": 2,
                                "name": "$title"
                            }
                        ]
                    },
                    {
                        "@type": "WebSite",
                        "@id": "$blogHomeUrl/#website",
                        "url": "$blogHomeUrl/",
                        "name": "$publisherName",
                        "description": "$publisherTagline",
                        "publisher": {
                            "@id": "$blogHomeUrl/#organization"
                        },
                        "inLanguage": "$language"
                    },
                    {
                        "@type": "Organization",
                        "@id": "$blogHomeUrl/#organization",
                        "name": "$publisherName",
                        "alternateName": "$publisherName",
                        "url": "$blogHomeUrl",
                        "logo": {
                            "@type": "ImageObject",
                            "inLanguage": "$language",
                            "@id": "$blogHomeUrl",
                            "url": "$logoImageUrl",
                            "contentUrl": "$logoImageUrl",
                            "caption": "$publisherName"
                        },
                        "image": {
                            "@id": "$blogHomeUrl"
                        },
                        "sameAs": [
                            "$facebookProfileLink",
                            "$threadsProfileLink",
                            "$instagramProfileLink",
                            "$linkedinProfileLink"
                        ]
                    },
                    {
                        "@type": "Person",
                        "@id": "$blogHomeUrl",
                        "name": "$publisherName"
                    }
                ]
            }
            </script>
        ';
    }


    
    // Read the existing tags.json file
    $tagsFilePath = __DIR__ . "/tags.json";
    $tagsData = file_exists($tagsFilePath) ? json_decode(file_get_contents($tagsFilePath), true) : ["hashtags" => []];

    // Process each tag and update the tags.json structure
    $tagsArray = explode(',', $tags);
    
    $formattedTagsForJson = array_map(function($tag) {
        $tag = trim($tag);
        if (strpos($tag, '#') !== 0) {
            $tag = '#' . $tag;
        }
        return $tag;
    }, $tagsArray);
    $formattedTagsString = implode(',', $formattedTagsForJson);

    $postFileName = $slug . ".html"; // The name of the HTML file being created
    foreach ($tagsArray as $tag) {
        $tag = trim($tag); // Trim any whitespace around the tag
        if (!isset($tagsData["hashtags"][$tag])) {
            $tagsData["hashtags"][$tag] = [];
        }

        // Append or update the data under the filename
        $tagsData["hashtags"][$tag][$postFileName] = [
            "title" => $title,
            "featuredImage" => $featuredImageUrl,
            "url" => $canonicalUrl,
            "category" => $category, // Include category in tags.json
            "visibility" => $visibility
        ];
    }

    // Remove tags no longer associated with the post
    foreach ($tagsData['hashtags'] as $tag => $posts) {
        if (!in_array($tag, $tagsArray)) {
            unset($tagsData['hashtags'][$tag][$postFileName]);
            if (empty($tagsData['hashtags'][$tag])) {
                unset($tagsData['hashtags'][$tag]);
            }
        }
    }

    // Write the updated data back to tags.json
    if (file_put_contents($tagsFilePath, json_encode($tagsData, JSON_PRETTY_PRINT)) === false) {
        die("Error: Unable to update tags.json.");
    }

    // Handle timestamp.json for recent posts
    $timestampFilePath = __DIR__ . "/timestamp.json";
    $timestampData = file_exists($timestampFilePath) ? json_decode(file_get_contents($timestampFilePath), true) : [];

    // Check if this post already exists in timestamp.json (by slug or URL)
    $existingTimestamp = null;
    foreach ($timestampData as $timestamp => $data) {
        if ($data['slug'] === $slug) {
            $existingTimestamp = $timestamp;
            break;
        }
    }

    // If the post exists, remove the old entry and delete associated files
    if ($existingTimestamp) {
        unset($timestampData[$existingTimestamp]);
        $existingPostFile = __DIR__ . "/" . $slug . ".html";
        if (file_exists($existingPostFile)) {
            unlink($existingPostFile); // Delete the old HTML file
        }

        // If a new image is uploaded, delete the old one
        if (!empty($_FILES['featuredImage']['name'])) {
            $oldImage = str_replace($rootPath, '', $timestampData[$existingTimestamp]['featuredImage']);
            $oldImagePath = __DIR__ . "/" . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old featured image
            }
        }
    }

// Capture the robotsMeta value from the form submission
$robotsMeta = isset($_POST['robotsMetaInput']) ? $_POST['robotsMetaInput'] : 'index, follow';

    $geoRegion = htmlspecialchars($_POST['geoRegion']);
    $geoPlacename = htmlspecialchars($_POST['geoPlacename']);
    $geoPosition = htmlspecialchars($_POST['geoPosition']);
    $ICBM = htmlspecialchars($_POST['ICBM']);
    
    $timestampData[$CurrentDateTime] = [
        "title" => $title,
        "featuredImage" => $featuredImage,
        "url" => $canonicalUrl,
        "firstLine" => $firstLine,
        "content" => $content,
        "focusKeyphrase" => $focusKeyphrase,
        "seoTitle" => $seoTitle,
        "slug" => $slug,
        "metaDescription" => $metaDescription,
        "tags" => $tags,
        "visibility" => $visibility,
        "category" => $category,
        "robotsMeta" => $robotsMeta, // Ensure this is saved
        "geoRegion" => $geoRegion,
        "geoPlacename" => $geoPlacename,
        "geoPosition" => $geoPosition,
        "ICBM" => $ICBM,
        "canonicalUrl" => $canonicalUrl, // Save canonical URL in timestamp.json
        "headScripts" => $headScripts,   // New key for head scripts
        "bodyScripts" => $bodyScripts,    // New key for body scripts
        "structuredData" => $structuredData
    ];

    // Write the updated data back to timestamp.json
    if (file_put_contents($timestampFilePath, json_encode($timestampData, JSON_PRETTY_PRINT)) === false) {
        die("Error: Unable to update timestamp.json.");
    }

    // Generate hashtag links
    $tagLinks = array_map(function($tag) {
        return '<a href="hashtagposts.html?tag=' . urlencode(trim($tag)) . '"> ' . htmlspecialchars(trim($tag)) . '</a>';
    }, $tagsArray);
    $tagLinksString = implode(', ', $tagLinks);

    // Create category links
    $categoryLinks = '<a href="categories.html?category=blog">Blog</a>, <a href="categories.html?category=case%20study">Case Study</a>';

    if ($visibility === 'public') {
        // Generate hashtag links
        $tagLinks = array_map(function($tag) {
            return '<a href="hashtagposts.html?tag=' . urlencode(trim($tag)) . '"> ' . htmlspecialchars(trim($tag)) . '</a>';
        }, $tagsArray);
        $tagLinksString = implode(', ', $tagLinks);
    

        // Check if robotsMeta is present in the form submission
        if (isset($_POST['robotsMeta'])) {
            $robotsMeta = htmlspecialchars($_POST['robotsMeta']);
        } else {
            // Default to 'index, follow' if not provided
            $robotsMeta = 'index, follow';
        }
        
        // Create the blog post content with updated styling and hashtag links
        $blogPostContent = <<<HTML
        <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="$robotsMeta" />
        <meta name="geo.region" content="$geoRegion" />
        <meta name="geo.placename" content="$geoPlacename" />
        <meta name="geo.position" content="$geoPosition" />
        <meta name="ICBM" content="$ICBM" />
        <title>$title</title>
        <link rel="shortcut icon" type="image/jpg" href="$favioconLink" />
        <meta name="description" content="$metaDescription" />
        <link rel="canonical" href="$canonicalUrl" />
        <meta property="og:locale" content="$language" />
        <meta property="og:type" content="$openGraphType" />
        <meta property="og:title" content="$seoTitle" />
        <meta property="og:description" content="$metaDescription" />
        <meta property="og:url" content="$canonicalUrl" />
        <meta property="article:publisher" content="$publisherUrl" />
        <meta property="article:published_time" content="$CurrentDateTime" />
        <meta name="author" content="$publisherName" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:creator" content="$publisherTwitterId" />
        <meta name="twitter:site" content="$publisherTwitterId" />
        <meta name="twitter:label1" content="Written by" />
        <meta name="twitter:data1" content="$publisherName" />
        <meta name="twitter:label2" content="Est. reading time" />
        <meta name="twitter:data2" content="4 minutes" />
        $structuredData
        $headScripts
        
        <link rel="stylesheet" href="blog.css"/>
        <link rel="stylesheet" href="stylesheet.css"/>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <!-- Font Awesome link -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <script src="https://kit.fontawesome.com/6c53136549.js" crossorigin="anonymous"></script>
        <script src="recentposts.js"></script> <!-- Add this line to include the recentposts.js script -->
    </head>
    <body>
    <header class="header">
        <div style="margin: 0;" class="row">
            <div style="padding: 0;" class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-5 nav_logo_col">
                <img onclick="window.location.href='index.html'" src="../img/inc_nav_logo.webp"
                    alt="INC Shipping Logo Design">
            </div>

            <!-- NAVBAR FOR DESKTOP -->
            <div style="padding: 0;"
                class="col-xl-6 col-lg-6 col-md-6 col-sm-0 col-xs-0 nav_navbar_col animate fadeInDown">
                <div class="navbar_container">
                    <div class="nav_main_menu_wrap">
                        <div class="nav_item ">
                            <a href="../index.html">Home</a>
                        </div>
                        <div class="nav_item">
                            <a href="../about.html">About Us</a>
                        </div>
                        <div class="nav_item nav_services">
                            <a href="../services.html">Services <i class="fa-solid fa-chevron-down"></i></a>
                            <div class="services_dropdown_container">
                                <div class="services_dropdown">
                                    <div class="services_dropdown_items_box">
                                        <a href="../air-freight.html"><i class="fa-solid fa-caret-right"></i> Air
                                            Freight</a>
                                        <a href="../sea-freight.html"><i class="fa-solid fa-caret-right"></i> Sea
                                            Freight</a>
                                        <a href="../road-freight.html"><i class="fa-solid fa-caret-right"></i> Road
                                            Freight</a>
                                        <a href="../customs-clearance.html"><i class="fa-solid fa-caret-right"></i>
                                            Customs Clearance</a>
                                        <a href="../temprature-controlled-cargo.html"><i
                                                class="fa-solid fa-caret-right"></i> Temp. Controlled Cargo</a>
                                        <a href="../warehousing.html"><i class="fa-solid fa-caret-right"></i>
                                            Warehousing</a>
                                        <a href="../cross-trade.html"><i class="fa-solid fa-caret-right"></i>
                                            Cross-Trade</a>
                                    </div>
                                    <div class="services_dropdown_items_box">
                                        <a href="../project-cargo.html"><i class="fa-solid fa-caret-right"></i> Project
                                            Cargo</a>
                                        <a href="../corporate-courier.html"><i class="fa-solid fa-caret-right"></i>
                                            Corporate Courier</a>
                                        <a href="../channel-partner.html"><i class="fa-solid fa-caret-right"></i>
                                            Channel Partner</a>
                                        <a href="../shipment-to-far-east.html"><i class="fa-solid fa-caret-right"></i>
                                            Far East Shipping</a>
                                        <a href="../dangerous-goods.html"><i class="fa-solid fa-caret-right"></i>
                                            Dangerous Goods</a>
                                        <a href="../healthcare.html"><i class="fa-solid fa-caret-right"></i> Healthcare
                                            Instruments Shipping</a>
                                        <a href="../pharma-shipment-handling.html"><i
                                                class="fa-solid fa-caret-right"></i> Pharma Shipment Handling</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nav_item nav_active">
                            <a href="../blog.html">Blogs</a>
                        </div>
                        <div class="nav_item">
                            <a href="../contact.html">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding: 0;" class="col-xl-2 col-lg-2 col-md-2 col-sm-0 col-0 header_contact_button">
                <button onclick="window.location.href='tel:+971503524424'">
                    <i class="fa-solid fa-phone"></i> +971 503524424
                </button>
            </div>
            <div style="padding: 0;" class="col-xl-1 col-lg-1 col-md-1 col-sm-0 col-0 nav_social_col">
                <div class="nav_social_container">
                    <a target="_blank" href="https://www.instagram.com/incexpressshipping/"><i
                            class="fa-brands fa-instagram"></i></a>
                    <a target="_blank" href="https://www.facebook.com/incshipping"><i
                            class="fa-brands fa-facebook"></i></a>
                    <a target="_blank" href="https://www.linkedin.com/company/inc-express-shipping-l-l-c/"><i
                            class="fa-brands fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="hamburger">
            <button onclick="hamburgerToggle()" class="hamburger_button">
                <i class="fa fa-bars"></i>
            </button>
        </div>

        <!-- MOBILE MENU -->

        <div class="hamburger_mobile_menu" id="hamburger_mobile_menu">
            <a href="../index.html">Home</a> <br>
            <a href="../about.html">About Us</a> <br>
            <div class="mobile_services_dropdown">
                <a href="#" id="services_link">Services <i class="fa-solid fa-chevron-down"></i></a> <br>
                <div class="mobile_services_dropdown_container" id="services_dropdown">
                    <a href="../air-freight.html"><i class="fa-solid fa-caret-right"></i> Air
                        Freight</a><br>
                    <a href="../sea-freight.html"><i class="fa-solid fa-caret-right"></i> Sea
                        Freight</a><br>
                    <a href="../road-freight.html"><i class="fa-solid fa-caret-right"></i> Road
                        Freight</a><br>
                    <a href="../customs-clearance.html"><i class="fa-solid fa-caret-right"></i>
                        Customs Clearance</a><br>
                    <a href="../temprature-controlled-cargo.html"><i class="fa-solid fa-caret-right"></i> Temp.
                        Controlled Cargo</a><br>
                    <a href="../warehousing.html"><i class="fa-solid fa-caret-right"></i>
                        Warehousing</a><br>
                    <a href="../cross-trade.html"><i class="fa-solid fa-caret-right"></i>
                        Cross-Trade</a><br>
                    <a href="../project-cargo.html"><i class="fa-solid fa-caret-right"></i> Project
                        Cargo</a><br>
                    <a href="../corporate-courier.html"><i class="fa-solid fa-caret-right"></i>
                        Corporate Courier</a><br>
                    <a href="../channel-partner.html"><i class="fa-solid fa-caret-right"></i>
                        Channel Partner</a><br>
                    <a href="../shipment-to-far-east.html"><i class="fa-solid fa-caret-right"></i>
                        Far East Shipping</a><br>
                    <a href="../dangerous-goods.html"><i class="fa-solid fa-caret-right"></i>
                        Dangerous Goods</a><br>
                    <a href="../healthcare.html"><i class="fa-solid fa-caret-right"></i> Healthcare
                        Instruments Shipping</a><br>
                    <a href="../pharma-shipment-handling.html"><i class="fa-solid fa-caret-right"></i> Pharma Shipment
                        Handling</a><br>
                </div>
            </div>
            <a href="../blog.html">Blogs</a> <br>
            <a href="../contact.html">Contact Us</a> <br><br>
            <a href="tel:+971503524424"> &nbsp; &nbsp;<i class="fa-solid fa-phone"></i>+971 503524424</a> <br>
        </div>
    </header>

    <div class="row base_container">
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12 base_container_col1">
            <div class="container">
                <img src="$featuredImage" class="featured-image" alt="Featured Image">
                <h1 class="post-title">$title</h1>
                <p class="post-meta">By $publisherName | $formattedPublishDate</p>
                <div class="post-content">$content</div>
                <p class="post-tags">Tags: $tagLinksString</p>
                <p class="post-categories">Category: $categoryLinks</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 base_container_col2">
            <h3>Recent posts:</h3>
                <div class="recentpost_card">
                    <h5><!--title of the latest post title of the latest post--> </h5>
                    <img src="url to featured image" alt="">
                    <p><!-- first line of the blogpost appears here--></p>
                    <a href="">Read more</a>
                </div>
                <!-- recent posts cards appear here like this -->
        </div>
    </div>

    <footer>
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="footer_logo_container">
                    <img onclick="window.location.href='index.html'" src="../img/inc_nav_logo.webp"
                        alt="INC Shipping Logo Design">
                    <p>INC Express Shipping LLC is one of the dedicated logistics, warehousing, and Best Logistics and
                        Shipping Company in Dubai, UAE.</p>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 footer_quick_links">
                        <div class="footer_link_heading">
                            <h4>QUICK LINKS</h4>
                        </div>
                        <div class="footer_links">
                            <a href="../index.html">Home</a>
                            <a href="../about.html">About Us</a>
                            <a href="../services.html">Services</a>
                            <a
                                href="../the-ultimate-guide-to-choosing-the-best-freight-forwarding-company-in-dubai-for-effortless-global-shipping.html">Blogs</a>
                            <a href="../contact.html">Contact Us</a>
                            <a href="../international-freight-forwarders.html">International Freight Forwarders</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 footer_quick_links">
                        <div class="footer_link_heading">
                            <h4>OUR SERVICES</h4>
                        </div>
                        <div class="footer_links">
                            <a href="../air-freight.html">Air Freight</a>
                            <a href="../sea-freight.html">Sea Freight</a>
                            <a href="../road-freight.html">Road Freight</a>
                            <a href="../customs-clearance.html">Customs Clearance</a>
                            <a href="../temprature-controlled-cargo.html">Temp. Controlled Cargo</a>
                            <a href="../warehousing.html">Warehousing</a>
                            <a href="../cross-trade.html">Cross Trade</a>
                            <a href="../project-cargo.html">Project Cargo</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 footer_quick_links">
                        <div class="footer_link_heading">
                            <h4>ADDRESS</h4>
                        </div>
                        <div class="footer_links">
                            <a href="">Office M29, Freight gate 4 Dubai
                                Airport Freezone Dubai, UAE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 col-12 footer_social_line">
                <div class="white_horizontal_line"></div>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
                <div class="footer_sociall_icons">
                    <a target="_blank" href="https://www.facebook.com/incshipping"><i
                            class="fa-brands fa-facebook"></i></a>
                    <a target="_blank" href="https://www.instagram.com/incexpressshipping/"><i
                            class="fa-brands fa-instagram"></i></a>
                    <a target="_blank" href=""><i class="fa-brands fa-twitter"></i></a>
                    <a target="_blank" href=""><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12 footer_copyright">
                <p>© 2024 INC Express Shipping LLC. All Rights Reserved. <a href="../privacy-policy.html">Privacy
                        Policy</a> | <a href="../terms-and-conditions.html">Terms of Service</a> | <a
                        href="../sitemap.html">Sitemap</a></p>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 footer_copyright">
                <p>Designed and Developed by: <a target="_blank" href="https://illforddigital.com/">Illford Digital</a>
                </p>
            </div>
        </div>
    </footer>
    <div class="floating_btn">
        <a target="_blank" href="https://wa.me/+971503524424" style="text-decoration: none;">
            <div class="contact_icon">
                <i class="fa fa-whatsapp my-float"></i>
            </div>
        </a>
        <p class="text_icon">Talk to us?</p>
    </div>
    <script src="../js/main.js"></script>
    
    <script src="recentposts.js"></script>
    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5pNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    $bodyScripts
    </body>
    </html>
HTML;


    // Save the blog post content to a file in the root directory
    $postFileName = __DIR__ . "/" . $slug . ".html";
    if (file_put_contents($postFileName, $blogPostContent) === false) {
        die("Error: Unable to save the blog post.");
    }

    echo "<script>alert('Post published successfully!'); window.location.href = 'admin.html';</script>";
} else {
    echo "<script>alert('Post saved as private'); window.location.href = 'admin.html';</script>";
}


    // Save the blog post content to a file in the root directory
    $postFileName = __DIR__ . "/" . $slug . ".html";
    if (file_put_contents($postFileName, $blogPostContent) === false) {
        die("Error: Unable to save the blog post.");
    }

    echo "<script>alert('Post published successfully!'); window.location.href = 'admin.html';</script>";
} else {
    echo "<script>alert('Error: Invalid request method.'); window.location.href = 'admin.html';</script>";
}



include_once('clear_temp_json.php');
include_once('clear_temp_json.php');
?>
