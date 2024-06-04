/**
 * Find elements with duplicate ids on same page
 */
function find_duplicate_ids() {
    const ids = {};
    const allTags = document.all || document.getElementsByTagName("*");
    
    for (let i = 0; i < allTags.length; i++) {
        let id = allTags[i].id;
        
        if (id) {
            if (ids[id]) {
                console.log("Duplicate id: #" + id);
            } else {
                ids[id] = 1;
            }
        }// if
    }// for
}// find_duplicate_ids

find_duplicate_ids();