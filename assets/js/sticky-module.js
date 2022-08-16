// || Remembering the tabs ("sticky" tabs)

// Because vanilla JS doesn't have a parents() function like jQuery does.
function getParents(el, parentSelector) {
  if (parentSelector === undefined) {
    parentSelector = document;
  }
  let parents = [];
  let p = el.parentNode;
  console.log("[DEBUG p = ", p);
  console.log("[DEBUG parentSelector = ", parentSelector);
  while (p !== parentSelector) {
    let o = p;
    parents.push(o);
    p = o.parentNode;
  }
  parents.push(parentSelector);
  return parents;
}

// Helper function
function makeHorizTabActive(selHorzItem) {
  document
    .querySelector(".horizontal-tabs > ul.tabs .tab")
    .classList.remove("active");
  document.querySelector(`a[href="${selHorzItem}"]`).closest("li.tab").click();
  document
    .querySelector(`a[href="${selHorzItem}"]`)
    .closest("li.tab")
    .classList.add("active");
}

// Export the main functions that does all the work.
export function makeTabsSticky() {
  console.log("[DEBUG] Popup Settings: Remembering the active tab item ...");

  const menuItemsSelector =
      ".pum-tabs-container.pum-tabbed-form.vertical-tabs > ul.tabs .tab",
    menuItems = document.querySelectorAll(menuItemsSelector),
    popupId = document.querySelector("#popup-id").dataset.popupId;

  console.log(`[DEBUG] Popup ID: ${popupId}`);

  if (!menuItems.length) {
    console.log("[DEBUG] Popup Settings: No tab items found!");
    return;
  }

  menuItems.forEach((mi) => {
    mi.addEventListener(
      "click",
      //(evt) => {
      function (evt) {
        if (!evt.target.hasAttribute("href")) return;
        sessionStorage.setItem(
          `${popupId}-active-tab`,
          evt.target.getAttribute("href")
        );

        // See if there's a horizontal tab saved for this.
        const currHorzTabName = evt.target.getAttribute("href").split("_")[1];
        const selHorzItem = sessionStorage.getItem(
          `${popupId}-active-horz-tab_${currHorzTabName}`
        );

        if (selHorzItem) {
          // Make the saved horizontal tab active.
          console.log(`[PUM] selHorzItem = ${selHorzItem}`);

          makeHorizTabActive(selHorzItem);
        } else {
          // If no horizontal tab saved, make the first tab active by default.
          //let $this = $( this ), // jQuery
          //	$container = $this.parents( '.pum-tabs-container:first' );

          const parentDiv = document.querySelector(".pum-tabs-container");
          const parents = getParents(this, parentDiv);

          //$container
          //	.find( '.horizontal-tabs > ul.tabs > li.tab:first-child' )
          //	.addClass('active'); // jQuery

          parents[parents.length - 1]
            .querySelector(".horizontal-tabs > ul.tabs > li.tab:first-child")
            .classList.add("active");
        }

        // Set up the horizontal tab listeners.
        //const tabCount = $( evt.target.getAttribute( "href" ) + ` .horizontal-tabs` ).data( 'tab-count' ); // jQuery

        const tabCount = document.querySelector(
          evt.target.getAttribute("href") + ` .horizontal-tabs`
        ).dataset.tabCount;

        console.log(`[PUM] tabCount = ${tabCount}`);

        if (tabCount > 1) {
          //const horzTabs = $( evt.target.getAttribute( "href" ) + ' .horizontal-tabs > ul.tabs .tab' ); // jQuery

          const horzTabs = document.querySelectorAll(
            evt.target.getAttribute("href") + " .horizontal-tabs > ul.tabs .tab"
          );

          console.log("[PUM] horzTabs = ", horzTabs);

          const horzTabName = evt.target.getAttribute("href").split("_")[1];
          horzTabs.forEach((el) => {
            el.addEventListener(
              "click",
              //(evt) => {
              function (evt) {
                if (!evt.target.hasAttribute("href")) return;
                sessionStorage.setItem(
                  `${popupId}-active-horz-tab_${horzTabName}`,
                  evt.target.getAttribute("href")
                );
              }
            ); // Listener
          }); // forEach
        } // if
      },
      false
    ); // Listener
  }); // forEach

  if (!sessionStorage.length) return;

  // If tabs were saved in session storage, then display them
  // if needed.

  const selMenuItem = sessionStorage.getItem(`${popupId}-active-tab`);
  if (!selMenuItem) return;
  const currHorzTabName = selMenuItem.split("_")[1];

  console.log(`[PUM] currHorzTabName = ${currHorzTabName}`);

  // Vertical tabs.
  if (selMenuItem) {
    const mi = document.querySelector(
      `${menuItemsSelector} > a[href="${selMenuItem}"`
    );
    if (!mi) {
      console.log("[DEBUG] Popup Settings: Active tab item not found!");
      return;
    } // if
    //mi.scrollIntoView();
    mi.click(); // Go to the section.
    //mi.focus();
    mi.classList.add("active"); // Highlight
  } // if

  const selHorzItem = sessionStorage.getItem(
    `${popupId}-active-horz-tab_${currHorzTabName}`
  );

  // Horizontal tabs.
  if (selHorzItem) {
    console.log(`[PUM] selHorzItem = ${selHorzItem}`);
    makeHorizTabActive(selHorzItem);
  }
} // makeTabsSticky()

/**
 * Add type attribute module to script tag
 * 
 function add_type_attribute($tag, $handle, $src) {
    if ( 'pum-admin-popup-editor' !== $handle ) {
        return $tag;
    }
    // Add the original ID and the type attributes.
    $tag = '<script id="pum-admin-popup-editor-js" type="module" src="' . esc_url( $src ) . '"></script>';
    return $tag;
}
add_filter('script_loader_tag', 'add_type_attribute' , 10, 3);
 *
 * We need to run this filter so the sticky-module.js file gets imported into 
 * admin-popup-editor.js.
 */