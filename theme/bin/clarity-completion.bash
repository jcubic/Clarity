# Bash completion for the clarity CLI
# https://clarity.pl.eu.org

_clarity() {
    local cur prev commands variants
    COMPREPLY=()
    cur="${COMP_WORDS[COMP_CWORD]}"
    prev="${COMP_WORDS[COMP_CWORD-1]}"

    commands="use create install remove list update uninstall version help"
    variants="canus dark_canus albus caeruleus lux_caeruleus violaceus lux_violaceus viridis luteus"

    case "$prev" in
        clarity)
            COMPREPLY=( $(compgen -W "$commands" -- "$cur") )
            return
            ;;
        use)
            local themes=""
            local community_dir="${HOME}/.clarity-icons/community"
            if [[ -d "$community_dir" ]]; then
                while IFS= read -r svg; do
                    local rel="${svg#${community_dir}/}"
                    themes="$themes @${rel%.svg}"
                done < <(find "$community_dir" -name "*.svg" -type f 2>/dev/null | sort)
            fi
            COMPREPLY=( $(compgen -W "$variants $themes" -- "$cur") )
            return
            ;;
        remove)
            local themes=""
            local community_dir="${HOME}/.clarity-icons/community"
            if [[ -d "$community_dir" ]]; then
                while IFS= read -r svg; do
                    local rel="${svg#${community_dir}/}"
                    themes="$themes @${rel%.svg}"
                done < <(find "$community_dir" -name "*.svg" -type f 2>/dev/null | sort)
            fi
            COMPREPLY=( $(compgen -W "$themes" -- "$cur") )
            return
            ;;
        create)
            COMPREPLY=( $(compgen -f -X '!*.svg' -- "$cur") )
            return
            ;;
    esac
}

complete -F _clarity clarity
