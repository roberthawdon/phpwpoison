<?php
// PHP Spam Poison (phpwpoison).
// 2004-2005 - Mario A. Valdez-Ramirez.
// 2017 - Robert Ian Hawdon.

// You can contact Mario A. Valdez-Ramirez
// by email at mario@mariovaldez.org or paper mail at
// Olmos 809, San Nicolas, NL. 66495, Mexico.

// Robert Ian Hawdon:
// https://robertianhawdon.me.uk/

// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or (at
// your option) any later version.

// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA


// ==================================
// Start of configuration options...
// ==================================
$pwp_scriptname = "";                                // Name of the parent script. THIS MUST BE SET BEFORE USING.
$pwp_standalone = true;                              // If the script is included in another, set to false.
$pwp_minemails = 5;                                  // Minimum emails per page.
$pwp_maxemails = 30;                                 // Maximum emails per page.
$pwp_maxlevel = 15;                                  // Deepest level to create links.
$pwp_word_file = "pwpwords.txt";                     // Source word file, relative to calling script.
$pwp_total_words = 99204;                            // Lines in source word file.
$pwp_cache_file = "cache.txt";                       // Cache word file, relative to calling script.
$pwp_cached_words = 300;                             // Words to extract from source word list.
$pwp_minword_len = 4;                                // Minimum length of words to use.
$pwp_maxword_len = 10;                               // Maximum length of words to use.
$pwp_cache_ttl = 7200;                               // Time before the cache file is rebuild (in seconds).
$pwp_minsleeptime = 10;                              // Minimum time to sleep before finishing page (in seconds).
$pwp_maxsleeptime = 30;                              // Maximum time to sleep before finishing page (in seconds).
$pwp_mintitle_words = 2;                             // Minimum words to use as title.
$pwp_maxtitle_words = 5;                             // Maximum words to use as title.
$pwp_mindummy_words = 10;                            // Minimum words to use in paragraphs.
$pwp_maxdummy_words = 25;                            // Maximum words to use in paragraphs.
$pwp_pre_dummypar = 2;                               // Numbers of paragraphs before email list.
$pwp_post_dummypar = 4;                              // Numbers of paragraphs after email list.
$pwp_presalt_user = 0;                               // Salt characters to add at the beginning of username in emails.
$pwp_postsalt_user = 2;                              // Salt characters to add at the ending of username in emails.
$pwp_presalt_dom = 2;                                // Salt characters to add at the beginning of domains in emails.
$pwp_postsalt_dom = 0;                               // Salt characters to add at the ending of domains in emails.
$pwp_numsalt_ratio = 10;                             // One out of X times a number is used as salt (0 = never).
$pwp_internat_ratio = 3;                             // One out of X times an international domain is used.
$pwp_link_firstratio = $pwp_maxdummy_words;          // One out of each X words is converted to a link in first paragraphs.
$pwp_link_lastratio = $pwp_mindummy_words;           // One out of each X words is converted to a link in last paragraphs.
$pwp_title_insertrate = 5;                           // Percentage of the time that the title is used as body text.
$pwp_symbol_ratio = 10;                              // One out of each X words is appended a punctuation symbol.
$pwp_use_spammer_list = false;                       // If we will include the spammer database as source of emails.
$pwp_spammer_file = "spammers.txt";                  // Spammer list file, relative to calling script.
$pwp_spammer_ratio = 5;                              // One out of X emails a spammer email will be used.
$pwp_spammer_generate = true;                        // If random emails will be generated using spammer domains.
$pwp_spammer_genratio = 2;                           // One out of X times the spammer email is generated.
// The following variables contain the appearance of the generated page, use them to match the appearance of your website.
$pwp_html_preheader = "<html><head><title>\n";
$pwp_html_postheader = "</title><meta NAME=\"ROBOTS\" CONTENT=\"NOINDEX, NOFOLLOW\">\n</head>\n<body>\n";
$pwp_html_footer = "</body></html>\n";
// ==================================
// End of configuration options...
// ==================================





$pwp_script_version = "1.3.0";

function fpwp_getenv($name, $default) {
  $env = getenv("PWP_" . $name);
  if (!$env) {
    return $default;
  }
  return $env;
}

// Get config from environment vars or leave as previously set above, if absent.
$pwp_scriptname = fpwp_getenv("SCRIPTNAME", $pwp_scriptname);

// Get the URL and split it, finding the target and the level...
$pwp_req_uri = $_SERVER["REQUEST_URI"];
if (!$pwp_req_uri) {
  $pwp_req_uri = $HTTP_SERVER_VARS["REQUEST_URI"];
  if (!$pwp_req_uri) {
    $pwp_req_uri = $_ENV["REQUEST_URI"];
    if (!$pwp_req_uri) {
      $pwp_req_uri = getenv("REQUEST_URI");
    }
    else $pwp_req_uri = "";
  }
}
$pwp_url_array = explode ("/", $pwp_req_uri);
$pwp_target = $pwp_url_array[(count ($pwp_url_array) - 1)];
if ($pwp_target == $pwp_scriptname) {
  $pwp_target = "";
}
else {
  unset ($pwp_url_array[(count ($pwp_url_array) - 1)]);
}
$pwp_level = preg_replace("/[^[:digit:]_]/", "", $pwp_target);
if (is_numeric ($pwp_level)) { $pwp_level = abs ($pwp_level); } else { $pwp_level = 0; }
$pwp_target = preg_replace("/[^[:alpha:]_]/", "", $pwp_target);
$pwp_scripturl = implode ("/", $pwp_url_array);


// Set some constants...
$pwp_common_symbols = array (".", ".", ".", ".", ",", ",", ",", ",", ",", ",", ",", ";", ";", ";", "?", "!");
$pwp_end_symbols = array (".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", "?", "!");

// Based on stats at http://www.webhosting.info/registries/global_stats/
$pwp_tldomains = array ("com", "com", "com", "com", "com", "com", "com", "com", "com", "com", "com",
                        "com", "com", "com", "com", "com", "com", "com", "com", "com", "com", "com",
                        "com", "com", "com", "com", "com", "com", "com", "com", "com", "com", "com",
                        "com", "com", "com", "com", "com", "com", "com", "com", "com", "com", "com",
                        "net", "net", "net", "net", "net", "net", "net", "net",
                        "net", "net", "net", "net", "net", "net", "net", "net",
                        "org", "org", "org", "org",
                        "biz", "biz", "info", "info", "edu", "gov");

// Based on stats at http://www.webhosting.info/domains/country_stats/
$pwp_ctldomains = array ("de", "de", "de", "de", "de", "de", "de", "de", "de", "de",
                         "de", "de", "de", "de", "de", "de", "de", "de", "de", "de",
                         "uk", "uk", "uk", "uk", "uk", "uk", "uk", "uk", "uk", "uk",
                         "uk", "uk", "uk", "uk", "uk", "uk", "uk", "uk", "uk", "uk",
                         "ca", "ca", "ca", "ca", "ca", "ca", "ca", "ca", "ca", "ca",
                         "ca", "ca", "ca", "ca", "ca", "ca", "ca", "ca", "ca", "ca",
                         "fr", "fr", "fr", "fr", "fr", "fr", "fr", "fr", "fr", "fr",
                         "cn", "cn", "cn", "cn", "cn", "cn", "cn", "cn", "cn", "cn",
                         "kr", "kr", "kr", "kr", "kr", "kr", "kr", "kr", "kr", "kr",
                         "au", "au", "au",
                         "jp", "jp", "jp",
                         "es", "es", "es",
                         "it", "it", "it",
                         "hk", "hk", "hk",
                         "nl", "nl", "nl",
                         "in", "in", "in",
                         "dk", "dk", "dk",
                         "tr", "tr", "tr",
                         "at", "at", "se", "se", "ky", "ky", "ch", "ch", "no", "no",
                         "fi", "be", "mx", "ru", "br", "vg", "pl", "th", "ie",
                         "cz", "sg", "ar", "my", "il", "ir", "nz", "tw", "pt", "za",
                         "bg", "id", "ro", "mc", "ua", "ve");



// Read the source word list, extract some words and store them in the cache file.
function fpwp_fill_wordcache ($sourcefilename, $targetfilename, $totalsourcewords, $totaltargetwords, $ttl) {
global $pwp_minword_len, $pwp_maxword_len;
  if ($totalsourcewords > $totaltargetwords) {
    $wordsratio = round ($totalsourcewords / $totaltargetwords);
  }
  else {
    $wordsratio = 1;
  }
  if (($sourcefilename) && ($targetfilename)) {
    if ((file_exists ($targetfilename) && is_writable ($targetfilename) && ((time () - filemtime ($targetfilename)) > $ttl)) || !file_exists ($targetfilename)) {
      if (file_exists ($sourcefilename) && is_readable ($sourcefilename)) {
        $sourcefile = fopen ($sourcefilename, "rb");
        $targetfile = fopen ($targetfilename, 'wb');
      }
      $ignorecount = 1;
      $randomline = rand (1, $wordsratio);
      if (($sourcefile) && ($targetfile)) {
        while ($wordcontent = fgets ($sourcefile, 1024)) {
          if ($ignorecount == $randomline) {
            $wordcontent = preg_replace("/[^[:alpha:]_]/", "", strtolower ($wordcontent)) . "\n";
            if ((strlen ($wordcontent) >= $pwp_minword_len) && (strlen ($wordcontent) <= $pwp_maxword_len)) {
              fwrite ($targetfile, $wordcontent);
            }
          }
          elseif ($ignorecount >= $wordsratio) {
            $ignorecount = 1;
            $randomline = rand (1, $wordsratio);
          }
          $ignorecount++;
        }
        @fclose ($targetfile);
        @fclose ($sourcefile);
      }
    }
  }
}


// Load the words from the cache file to memory.
function fpwp_load_words ($sourcefilename, $pagetitle, $insertrate) {
  if (file_exists ($sourcefilename) && is_readable ($sourcefilename)) {
    $wordlist = file ($sourcefilename);
    $tinserts = round (count ($wordlist) / (100 / $insertrate));
    if (count ($wordlist) > 0) {
      foreach ($wordlist as $key => $value) { $wordlist[$key] = trim ($value); }
    }
    if ($pagetitle) {
      for ($wc = 0; $wc < $tinserts; $wc++) { $wordlist[] = $pagetitle; }
    }
    return $wordlist;
  }
  else {
    return false;
  }
}


// Load the spammer emails from the spammers file to memory.
function fpwp_load_spammers ($sourcefilename) {
  if (file_exists ($sourcefilename) && is_readable ($sourcefilename)) {
    $spammerlist = file ($sourcefilename);
    return $spammerlist;
  }
  else {
    return false;
  }
}


// Add a short random string to the beginning or end of a given string.
function fpwp_add_salt ($textstr, $presalt, $postsalt) {
global $pwp_numsalt_ratio;
  $presaltstr = "";
  $postsaltstr = "";
  if ($presalt > 0) {
    for ($sc = 1; $sc <= $presalt; $sc++) {
      if (rand (1, $pwp_numsalt_ratio) == $pwp_numsalt_ratio) {
        $presaltstr .= chr (rand (ord ("0"), ord ("9")));
      }
      else {
        $presaltstr .= chr (rand (ord ("a"), ord ("z")));
      }
    }
  }
  if ($postsalt > 0) {
    for ($sc = 1; $sc <= $postsalt; $sc++) {
      if (rand (1, $pwp_numsalt_ratio) == $pwp_numsalt_ratio) {
        $postsaltstr .= chr (rand (ord ("0"), ord ("9")));
      }
      else {
        $postsaltstr .= chr (rand (ord ("a"), ord ("z")));
      }
    }
  }
  return ($presaltstr . $textstr . $postsaltstr);
}


// Convert to uppercase the first letter of each sentence.
function fpwp_ucfirst ($string) {
global $pwp_end_symbols;
  if ($string) {
    $strarray = explode (" ", $string);
    $totwords = count ($strarray);
    $restart = false;
    for ($cw = 0; $cw < $totwords; $cw++) {
      if ($restart) {
        $strarray[$cw] = ucfirst ($strarray[$cw]);
      }
      $restart = in_array (substr ($strarray[$cw], -1), $pwp_end_symbols);
    }
    $strarray[0] = ucfirst ($strarray[0]);
    return (implode (" ", $strarray));
  }
  return ("");
}


// Create a paragraph using the word list, optionally create links within.
function fpwp_build_dummytext ($totalwords, &$wordlist, $linkratio, $title) {
global $pwp_common_symbols, $pwp_end_symbols, $pwp_scripturl, $pwp_symbol_ratio, $pwp_level, $pwp_maxlevel;
  if (count ($wordlist) > 1) {
    shuffle ($wordlist);
    if ($totalwords > count ($wordlist)) {
      $totalwords = count ($wordlist);
    }
    $newlist = array_rand ($wordlist, $totalwords);
    $newtext = "";
    foreach ($newlist as $word) {
      if ((rand (1, $linkratio) == $linkratio) && ($wordlist[$word] != $title) && ($pwp_level < $pwp_maxlevel)) {
        $insertpos = rand (1, strlen ($wordlist[$word])) - 1;
        $newlink = substr ($wordlist[$word], 0, $insertpos) . ($pwp_level + 1) . substr ($wordlist[$word], $insertpos, strlen ($wordlist[$word]));
        $newlink = $pwp_scripturl . "/" . $newlink;
        $newtext .= "\n<a href=\"" . $newlink . "\">" . $wordlist[$word] . "</a>";
      }
      else {
        $newtext .= $wordlist[$word];
      }
      if (rand (1, $pwp_symbol_ratio) == $pwp_symbol_ratio) {
        $newtext .= $pwp_common_symbols[array_rand ($pwp_common_symbols, 1)];
      }
      $newtext .= " ";
    }
    $newtext = substr ($newtext, 0, -1);
    if (in_array (substr ($newtext, -1), $pwp_end_symbols)) {
      $newtext = substr ($newtext, 0, -1);
    }
    $newtext = fpwp_ucfirst ($newtext);
    $newtext .= $pwp_end_symbols[array_rand ($pwp_end_symbols, 1)];
    return $newtext;
  }
}


// Create a domain name.
function fpwp_build_domain (&$wordlist) {
global $pwp_tldomains, $pwp_ctldomains, $pwp_internat_ratio;
  $newdomain = $wordlist[array_rand ($wordlist, 1)];
  $newdomain .= "." . $pwp_tldomains[array_rand ($pwp_tldomains, 1)];
  if (rand (1, $pwp_internat_ratio) == $pwp_internat_ratio) {
    $newdomain .= "." . $pwp_ctldomains[array_rand ($pwp_ctldomains, 1)];
  }
  return $newdomain;
}

// Create an username.
function fpwp_build_username (&$wordlist) {
  return $wordlist[array_rand ($wordlist, 1)];
}

// Extract domain name from email address.
function fpwp_extract_domain ($email) {
  return strstr ($email, "@");
}

// Create an email link.
function fpwp_build_maillist ($totalmails, &$wordlist, &$spammerlist) {
global $pwp_presalt_user, $pwp_postsalt_user, $pwp_presalt_dom, $pwp_postsalt_dom, $pwp_spammer_ratio, $pwp_spammer_generate, $pwp_spammer_genratio;
  echo "<p>\n";
  for ($ce = 1; $ce < $totalmails; $ce++) {
    $newemail = "";
    if ($spammerlist && (rand (1, $pwp_spammer_ratio) == $pwp_spammer_ratio)) {
      if ($pwp_spammer_generate && (rand (1, $pwp_spammer_genratio) == $pwp_spammer_genratio)) {
        $newuser = fpwp_add_salt (fpwp_build_username ($wordlist), $pwp_presalt_user, $pwp_postsalt_user);
        $newdom = fpwp_extract_domain (trim ($spammerlist[array_rand ($spammerlist, 1)]));
        if ($newdom) {
          $newemail = $newuser . $newdom;
        }
      }
      else {
        $newemail = trim ($spammerlist[array_rand ($spammerlist, 1)]);
      }
    }
    else {
      $newuser = fpwp_add_salt (fpwp_build_username ($wordlist), $pwp_presalt_user, $pwp_postsalt_user);
      $newdom = fpwp_add_salt (fpwp_build_domain ($wordlist), $pwp_presalt_dom, $pwp_postsalt_dom);
      $newemail = $newuser . "@" . $newdom;
    }
    if ($newemail) {
      echo "<a href=\"mailto:" . $newemail . "\">" . $newemail . "</a><br>\n";
    }
  }
  echo "</p>\n";
}




//#####################################
// Main program.
//#####################################
if ($pwp_scriptname) {
  if ($pwp_standalone) { echo $pwp_html_preheader . $pwp_target . $pwp_html_postheader; }
  fpwp_fill_wordcache ($pwp_word_file, $pwp_cache_file, $pwp_total_words, $pwp_cached_words, $pwp_cache_ttl);
  $pwp_words = fpwp_load_words ($pwp_cache_file, $pwp_target, $pwp_title_insertrate);
  if ($pwp_use_spammer_list) {
    $pwp_spammers = fpwp_load_spammers ($pwp_spammer_file);
  }
  else {
    $pwp_spammers = false;
  }
  if ($pwp_words) {
    echo "<h1>" . fpwp_build_dummytext ($pwp_mintitle_words + rand (0, ($pwp_maxtitle_words - $pwp_mintitle_words)), $pwp_words, 0, $pwp_target) . "</h1>\n";
    for ($pc = 1; $pc <= $pwp_pre_dummypar; $pc++) {
      echo "<p>" . fpwp_build_dummytext ($pwp_mindummy_words + rand (0, ($pwp_maxdummy_words - $pwp_mindummy_words)), $pwp_words, $pwp_link_firstratio, $pwp_target) . "</p>\n";
    }
    echo fpwp_build_maillist ($pwp_minemails + rand (0, ($pwp_maxemails - $pwp_minemails)), $pwp_words, $pwp_spammers);
    if ($pwp_maxsleeptime > 0) {
      sleep ($pwp_minsleeptime + rand (0, ($pwp_maxsleeptime - $pwp_minsleeptime)));
    }
    for ($pc = 1; $pc <= $pwp_post_dummypar; $pc++) {
      echo "<p>" . fpwp_build_dummytext ($pwp_mindummy_words + rand (0, ($pwp_maxdummy_words - $pwp_mindummy_words)), $pwp_words, $pwp_link_lastratio, $pwp_target) . "</p>\n";
    }
  }
  if ($pwp_standalone) { echo $pwp_html_footer; }
}
else echo "<p>DON'T FORGET TO CONFIGURE THE SCRIPT!</p>";



?>
