find /archive/2016/08/ \( -iname "ccm*" \) -type f -print0 | xargs -0 zgrep --color -n "$1"
