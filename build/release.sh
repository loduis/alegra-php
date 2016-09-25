set -e
echo "Enter release version: "
read VERSION

read -p "Releasing $VERSION - are you sure? (y/n)" -n 1 -r
echo    # (optional) move to a new line

ROOT_PROJECT=$(realpath "${0}")
ROOT_PROJECT=$(dirname "${ROOT_PROJECT}")
ROOT_PROJECT=$(dirname "${ROOT_PROJECT}")
echo $ROOT_PROJECT

if [[ $REPLY =~ ^[Yy]$ ]]
then
  echo "Releasing $VERSION ..."

  cd $ROOT_PROJECT

  phpunit

  # commit
  git add -A
  git commit -m "[build] $VERSION"

  # publish
  git push origin refs/tags/v$VERSION
  git push

  cd -
fi
