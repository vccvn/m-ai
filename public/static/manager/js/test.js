!function () {
    function generateRandom(min = 0, max = 100) {

        // find diff
        let difference = max - min;

        // generate random number
        let rand = Math.random();

        // multiply with difference
        rand = Math.floor( rand * difference);

        // add with min value
        rand = rand + min;

        return rand;
    }

    function test(turn = 1) {
        let is_correct = 0;
        let is_incorrect = 0;

        for (let index = 0; index < turn; index++) {
            let correct = generateRandom(1, 3);
            let choose = 0;
            let show = 0;
            let sa = true;
            do {
                choose = generateRandom(1, 3);
                if(!(choose == 0 || choose == correct)) sa = false;
            } while (sa);
            let s = true;
            do {
                show = generateRandom(1, 3);
                if(!(show == 0 || show == correct || show == choose)) s = false;
            } while (s);
            let t = true;
            do {
                let tmp = generateRandom(1, 3);
                if(tmp != show && tmp != choose){
                    choose = tmp;
                    t = false;
                }
            } while (t);
            if(choose == correct) is_correct ++;
            else is_incorrect ++;

        }

        console.log("turn: " + turn);
        console.log("correct: " + is_correct);
        console.log("incorrect: " + is_incorrect);

    }
}();
