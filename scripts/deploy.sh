#!/usr/bin/env bash
set -Eeuo pipefail

: "${HEBS_IMAGE:?HEBS_IMAGE is required}"
: "${HEBS_DATA_DIR:?HEBS_DATA_DIR is required}"
: "${HEBS_ENV_FILE:?HEBS_ENV_FILE is required}"

compose_file="${HEBS_COMPOSE_FILE:-compose.production.yml}"
project_name="${COMPOSE_PROJECT_NAME:-hebs}"

if [[ "${HEBS_DATA_DIR}" != /* ]]; then
    echo "HEBS_DATA_DIR must be an absolute path." >&2
    exit 1
fi

if [[ ! -s "${HEBS_ENV_FILE}" ]]; then
    echo "HEBS_ENV_FILE does not exist or is empty: ${HEBS_ENV_FILE}" >&2
    exit 1
fi

if [[ ! -f "${compose_file}" ]]; then
    echo "Compose file does not exist: ${compose_file}" >&2
    exit 1
fi

mkdir -p "${HEBS_DATA_DIR}/database" "${HEBS_DATA_DIR}/uploads"

compose=(docker compose --project-name "${project_name}" --file "${compose_file}")
previous_container="$("${compose[@]}" ps --quiet web 2>/dev/null || true)"
previous_image=""

if [[ -n "${previous_container}" ]]; then
    previous_image="$(docker inspect --format '{{.Config.Image}}' "${previous_container}")"
fi

echo "Pulling ${HEBS_IMAGE}"
"${compose[@]}" pull web

echo "Deploying ${HEBS_IMAGE}"
if "${compose[@]}" up --detach --remove-orphans --wait --wait-timeout 180 web; then
    release_file="${HEBS_DATA_DIR}/current-image"
    if [[ -n "${previous_image}" && "${previous_image}" != "${HEBS_IMAGE}" ]]; then
        printf '%s\n' "${previous_image}" > "${HEBS_DATA_DIR}/previous-image.tmp"
        mv "${HEBS_DATA_DIR}/previous-image.tmp" "${HEBS_DATA_DIR}/previous-image"
    fi
    printf '%s\n' "${HEBS_IMAGE}" > "${release_file}.tmp"
    mv "${release_file}.tmp" "${release_file}"
    "${compose[@]}" ps
    exit 0
fi

echo "Deployment health check failed." >&2

if [[ -n "${previous_image}" && "${previous_image}" != "${HEBS_IMAGE}" ]]; then
    echo "Rolling the application container back to ${previous_image}." >&2
    HEBS_IMAGE="${previous_image}" "${compose[@]}" up --detach --remove-orphans --wait --wait-timeout 180 web || true
fi

"${compose[@]}" ps >&2 || true
"${compose[@]}" logs --no-color --tail 100 web >&2 || true
exit 1
