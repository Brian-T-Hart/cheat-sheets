# Child Theme

Content related to using or updating a child theme

## Updating a Child Theme Name

- Make a backup of the site

- Update the name of the child theme folder (example: bricks-child => custom-name)

- Update Theme Name in the styles.css file within the child theme (example: Bricks Child Theme => Custom Name - Bricks Child Theme)

- Update the following values in the wp_options database

  - stylesheet => set to directory name (example: custom-name)

  - theme_mods => update directory name (example: theme_mods_bricks-child => theme_mods_custom-name)

  - current_theme => set to theme name from styles.css (example: EXPTRAC - Bricks Child Theme)
  