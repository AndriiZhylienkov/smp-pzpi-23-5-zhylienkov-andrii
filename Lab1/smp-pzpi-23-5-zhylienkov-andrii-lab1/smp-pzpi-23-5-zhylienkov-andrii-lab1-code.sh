#!/bin/bash

if [[ "$1" == "--help" ]]; then
    echo "Usage: smp-pzpi-23-5-zhylienkov-andrii-lab1-code [--help | --version] | [[-q|--quiet] [ПЗПІ-23-5] TimeTable_11_04_2025]"
    echo "--help    Display this help message."
    echo "--version Show the version of the script."
    echo "-q, --quiet Suppress output."
    exit 0
fi

if [[ "$1" == "--version" ]]; then
    echo "smp-pzpi-23-5-zhylienkov-andrii-lab1-code version 1.0"
    exit 0
fi

quiet=false
if [[ "$1" == "-q" || "$1" == "--quiet" ]]; then
    quiet=true
    shift
fi

log() {
    if [[ $quiet != true ]]; then
        echo "$@"
    fi
}

log "Available timetable files:"
files=(*.csv)
select file in "${files[@]}"; do
    if [[ -n "$file" ]]; then
        log "You selected: $file"
        break
    fi
done

temp_file="$(mktemp)"
iconv -f WINDOWS-1251 -t UTF-8 "$file" | sed 's/\r/\n/g' > "$temp_file" && mv "$temp_file" "$file"

mapfile -t groups < <(
    awk -F',' 'NR>1 {
        if (match($1, /ПЗПІ-23-[1-5]/, arr)) {
            print arr[0]
        } else if (match($1, /\(ПЗПІ-23-\)-[1-5]/, arr)) {
            gsub("[()]", "", arr[0])
            print arr[0]
        } else if (match($1, /ПЗПІ-23-[1-5],[1-5]/, arr)) {
            split(arr[0], groupArray, /,/)
            for (i in groupArray) print groupArray[i]
        }
    }' "$file" | sort -u
)

if [[ ${#groups[@]} -eq 0 ]]; then
    log "No academic groups found in file!"
    exit 1
fi

log "Available academic groups in $file:"
select group in "${groups[@]}"; do
    if [[ -n "$group" ]]; then
        log "You selected: $group"
        break
    fi
done

output_file="${group}.csv"
group_num="${group: -1}"

awk -F',' -v group="$group" -v gnum="$group_num" 'NR==1 { print; next }
{
    line = $0
    gsub("\"", "", line)
    if (line ~ "(^|[^0-9])" group "([^0-9]|$)" || line ~ "\\(ПЗПІ-23-\\)-" gnum)
        print $0
}' "$file" > "$output_file"

date_part=$(basename "$file" | sed -n 's/^TimeTable_\([0-9]\{2\}_[0-9]\{2\}_[0-9]\{4\}\)\.csv$/\1/p')

if [[ -n "$date_part" ]]; then
    google_output="Google_TimeTable_${date_part}.csv"
else
    google_output="Google_TimeTable_output.csv"
fi

log "Subject,Start Date,Start Time,End Date,End Time,All Day Event,Description" > "$google_output"

awk -F',' '
BEGIN {
    log_header = "Subject,Start Date,Start Time,End Date,End Time,All Day Event,Description";
    print log_header > "'"$google_output"'"
}

NR > 1 {
    gsub("\"", "", $0)
    raw_subject = $1
    gsub(/^[0-9]+ /, "", raw_subject)

    match(raw_subject, /([А-ЯІЇа-яіїA-Za-z0-9\-\s]+)\s+(Лб|Пз|Лк)/, m)
    if (!m[1] || !m[2]) {
        next
    }

    subject_name = m[1]
    subject_type = m[2]
    key = subject_name "-" subject_type

    if (subject_type == "Лб") {
        count[key]++
        pair_index = int((count[key]+1)/2)
    } else {
        count[key]++
        pair_index = count[key]
    }

    full_subject = raw_subject "; №" pair_index

    split($2, sd, ".")
    split($4, ed, ".")
    start_date = sd[2] "/" sd[1] "/" sd[3]
    end_date   = ed[2] "/" ed[1] "/" ed[3]

    split($3, st, ":")
    hour = st[1]; min = st[2]; sec = st[3]
    if (hour >= 12) {
        if (hour > 12) hour -= 12
        ampm = "PM"
    } else {
        if (hour == 0) hour = 12
        ampm = "AM"
    }
    start_time = sprintf("%02d:%02d:%02d %s", hour, min, sec, ampm)

    split($5, et, ":")
    hour = et[1]; min = et[2]; sec = et[3]
    if (hour >= 12) {
        if (hour > 12) hour -= 12
        ampm = "PM"
    } else {
        if (hour == 0) hour = 12
        ampm = "AM"
    }
    end_time = sprintf("%02d:%02d:%02d %s", hour, min, sec, ampm)

    desc = $12

    printf "\"%s\",%s,%s,%s,%s,False,\"%s\"\n", full_subject, start_date, start_time, end_date, end_time, desc >> "'"$google_output"'"
}
' "$output_file"

log "Google Calendar CSV generated: '$google_output'"
rm -f "$output_file"
