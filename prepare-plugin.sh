#!/bin/bash

NEW_ABBR="CBQEP_"
NEW_BASE="custom-bulk-quick-edit-premium"
NEW_CLASS="Custom_Bulk_Quick_Edit_Premium"
NEW_KB_PATH="20112546-Custom-Bulk-Quick-Edit"
NEW_SITE=""
NEW_SLUG="cbqep_"
NEW_SLUG_LONG="custom_bulk_quick_edit_premium"
NEW_TITLE="Custom Bulk/Quick Edit Premium"
NEW_FILTER="${NEW_SLUG}"

OLD_ABBR="WPSP_"
OLD_BASE="wordpress-starter-premium"
OLD_CLASS="WordPress_Starter_Premium"
OLD_KB_PATH="20102742-WordPress-Starter-Plugin"
OLD_SITE="http://wordpress.org/plugins/wordpress-starter-premium/"
OLD_SLUG="wpsp_"
OLD_SLUG_LONG="wordpress_starter_premium"
OLD_TITLE="WordPress Starter Premium"
OLD_FILTER="${OLD_SLUG}"

echo
echo "Begin converting ${OLD_TITLE} to ${NEW_TITLE} plugin"

FILES=`find . -type f \( -name "*.css" -o -name "*.md" -o -name "*.php" -o -name "*.txt" -o -name "*.xml" \)`
for FILE in ${FILES} 
do
	if [[ '' != ${NEW_ABBR} ]]
	then
		perl -pi -e "s#${OLD_ABBR}#${NEW_ABBR}#g" ${FILE}
		perl -pi -e "s#${NEW_ABBR}_#${NEW_ABBR}#g" ${FILE}
	fi

	if [[ '' != ${NEW_BASE} ]]
	then
		perl -pi -e "s#${OLD_BASE}#${NEW_BASE}#g" ${FILE}
	fi

	if [[ '' != ${NEW_CLASS} ]]
	then
		perl -pi -e "s#${OLD_CLASS}#${NEW_CLASS}#g" ${FILE}
	fi

	if [[ '' != ${NEW_FILTER} ]]
	then
		perl -pi -e "s#${OLD_FILTER}#${NEW_FILTER}#g" ${FILE}
	fi

	if [[ '' != ${NEW_KB_PATH} ]]
	then
		perl -pi -e "s#${OLD_KB_PATH}#${NEW_KB_PATH}#g" ${FILE}
	fi

	if [[ '' != ${NEW_SITE} ]]
	then
		perl -pi -e "s#${OLD_SITE}#${NEW_SITE}#g" ${FILE}
	fi

	if [[ '' != ${NEW_SLUG} ]]
	then
		perl -pi -e "s#${OLD_SLUG}#${NEW_SLUG}#g" ${FILE}
		perl -pi -e "s#${NEW_SLUG}_#${NEW_SLUG}#g" ${FILE}
	fi

	if [[ '' != ${NEW_SLUG_LONG} ]]
	then
		perl -pi -e "s#${OLD_SLUG_LONG}#${NEW_SLUG_LONG}#g" ${FILE}
	fi

	if [[ '' != ${NEW_TITLE} ]]
	then
		perl -pi -e "s#${OLD_TITLE}#${NEW_TITLE}#g" ${FILE}
	fi
done

if [[ -e 000-code-qa.txt ]]
then
	rm 000-code-qa.txt
fi

mv ${OLD_BASE}.css ${NEW_BASE}.css
mv ${OLD_BASE}.php ${NEW_BASE}.php
mv languages/${OLD_BASE}.pot languages/${NEW_BASE}.pot
mv lib/class-${OLD_BASE}-licensing.php lib/class-${NEW_BASE}-licensing.php

if [[ -e .git ]]
then
	rm -rf .git
fi

if [[ -e lib/aihrus ]]
then
	rm lib/aihrus
fi

git init
git add *
git add .gitignore
git commit -m "Initial plugin creation"
git remote add origin git@github.com:michael-cannon/${NEW_BASE}.git

git remote add aihrus git@github.com:michael-cannon/aihrus-framework.git
git fetch aihrus 
git subtree add -P lib/aihrus --squash aihrus master
git commit -a -m "Link in Aihrus Framework"
echo "git push origin master"