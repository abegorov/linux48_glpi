#!/bin/bash
set -eux -o pipefail

AGE={{ backup_mariadb_age | ansible.builtin.quote }}
NAME={{ backup_mariadb_name | ansible.builtin.quote }}
USR={{ backup_mariadb_user | ansible.builtin.quote }}
HST={{ backup_mariadb_host | ansible.builtin.quote }}

mkdir -p "/srv/backup/mariadb/${NAME}"
find "/srv/backup/mariadb/${NAME}" \
  -type f -mtime "${AGE}" -name '*.zstd' -delete

ARCHIVE="/srv/backup/mariadb/${NAME}/$(date --iso-8601=seconds).zstd"
ssh "${USR}"@"${HST}" \
  'mariabackup --backup --stream=xbstream | zstd - -c' > "${ARCHIVE}"
