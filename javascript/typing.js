const alphabet = ['a','b','c','d','e','f','g','h','i','j','k','l',
                    'm','n','o','p','q','r','s','t','u','v','w','x','y','z'];
let str;
let score_by20;
let start_time;
let end_time = 60;
let remaining_time = end_time;
let now_score = 0;
timer.innerText = ("0".repeat(2) + end_time).slice(-3);

function start_timer() {
    start_button.innerText = "リスタート";
    timer.innerText = ("0".repeat(2) + end_time).slice(-3);
    start_time = new Date();
    let timerId = setInterval(() => {
        remaining_time = end_time - get_time();
        timer.innerText = ("0".repeat(2) + remaining_time).slice(-3);
        if(remaining_time <= 0) {           
            clearInterval(timerId);
            time_up();
        }
    }, 1000);   //1秒毎に呼び出す
}

function time_up() {
    document.getElementById('string_input').disabled = true;
    document.getElementById('end_score_submit').disabled = false;
    string_display.innerText = "";
    const time_up_p = document.createElement("p");
    time_up_p.classList.add("time-up");
    time_up_p.innerText = "Time Up";
    string_display.appendChild(time_up_p);
}

function create_string(){
    str = "";
    string_display.innerText = "";
    for (let i = 0; i < 20; i++){
        str += alphabet[Math.floor(Math.random() * alphabet.length)];
    }
    const chars = str.split('');

    /* 色を付けるために、spanで分ける */
    chars.forEach((char) => {
        const char_span = document.createElement("span");
        char_span.innerText = char;
        string_display.appendChild(char_span);
    });
}

/* 文字の正誤判定 */
string_input.addEventListener("input", () => {
    const char_span_node = string_display.querySelectorAll("span");
    const input_chars = string_input.value.split('');
    char_span_node.forEach((char_span, index) => {
        if(input_chars[index] == null){
            char_span.classList.remove("correct");
            char_span.classList.remove("incorrect");
        }
        else if(char_span.innerText == input_chars[index]){
            char_span.classList.add("correct");
            char_span.classList.remove("incorrect");
        } else{
            char_span.classList.add("incorrect");
            char_span.classList.remove("correct");
        }
    })
    
    if(remaining_time > 0 && remaining_time < end_time){
        const class_correct = document.getElementsByClassName("correct");
        now_score = score_by20 + class_correct.length;
        score.innerText = now_score;
        if(class_correct.length == str.length){
            create_string();
            string_input.value = '';
            score_by20 += 20;
        }
    }
});

function get_time() {
    return Math.floor((new Date() - start_time) / 1000);
}

document.addEventListener('keydown', function(e) {
    input_focus();
    //console.log(e.key);
    if(e.key === "Escape" || e.key === "Enter"){
        start();
    }
})

function input_focus() {
    document.getElementById("string_input").focus();
}

function start() {
    score_by20 = 0;
    string_input.value = '';
    score.innerText = 0;
    document.getElementById('string_input').disabled = false;
    input_focus();
    create_string();
    start_timer();
}

function score_submit() {
    end_score.value = now_score;
}

function score_submit_confirm() {
    if(now_score > 0) {
        if(window.confirm("スコアを記録せずに移動しますか？")) {
            window.location.href='ranking.php';
        }
    } else {
        window.location.href='ranking.php';
    }
}