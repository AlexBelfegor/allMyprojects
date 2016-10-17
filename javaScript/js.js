/**
 * Created by vetal on 6/23/2016.

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n)
}
function calc(num) {

    var arr = [], sum = 0;
    while (num != null && isNumeric(num)) {
        arr.push(num);
        num = prompt('Введите число', 'Число');
    }
    for (var i = 0; i < arr.length; i++) {
        sum += +arr[i];
    }
    return alert(sum);

}

var input = prompt('Введите число', 'Число');
calc(input);

function filterRange(arr, a, b) {

    var arr2 = [];
    for (var i = 0; i < arr.length; i++) {
       if (arr[i] >=a && arr[i] <= b) {
            arr2.push(arr[i]);
        }
    }
    return arr2;

}
var arr = [5, 4, 3, 8, 0, 2, 3];
alert(filterRange(arr, 2, 6));

function elNum(n){
    var arr = [], arr2 = [];
    for (var i = 0; i <= n; i++) {
        arr[i] = i + 2;
        if (arr[i] % 2 == 0 && i > 2) {
            arr2.push(arr[i]);
        }
    }
    return arr2;
}
alert(elNum(10));

var n = 100;
var arr = [];
var simple = [];
var sum = 0;

for (var i = 2; i <= n; i++) {
    for (var p = 2; p*p <= n; p++) {
        simple[p*i] = 0;
    }
    if (simple[i] !== 0) {
        arr.push(i);
        sum += i;
    }
}
alert(simple);

function camelize(str) {

    var arr = str.split('-');
    for (var i = 1; i < arr.length; i++) {
        arr[i] = arr[i].toUpperCase().charAt(0) + arr[i].slice(1);
    }
    return arr.join('');

}
var str = 'background-color';
alert(camelize(str));

function addClass(obj, cls) {

    var str = obj.className;
    if (str.indexOf(cls) == -1) {
        if (str[0] == undefined) {
            str = cls;
        } else {
            str = str + ' ' + cls;
        }
    }
    return obj.className = str;

}

function addClass2(obj, cls) {

    var arr = obj.className ? obj.className.split(' ') : [];
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == cls) {
            return;
        }
    }
    arr.push(cls);
    return obj.className = arr.join(' ');
}
alert(addClass2(obj, 'open'));
var obj = {className: 'open menu menu menu now'};

function removeClass(obj, cls) {
    var arr = obj.className ? obj.className.split(' ') : [];
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == cls) {
            arr.splice(i, 1);
            i--;
        }
    }
    return obj.className = arr.join(' ');
}
alert(removeClass(obj, 'menu'));

function filterRangeInPlace(arr, a, b) {

    for (var i = 0; i < arr.length; i++) {
        if (arr[i] >= a && arr[i] <= b) {

        } else {
            arr.splice(i, 1);
        }
    }
    return arr;

}
function sortN (a, b) {

    return Math.random() - 0.5;
}
var arr = [2, 1, 3, 4, 5];

arr.sort(sortN);
alert(arr);

var vasya = { name: "Вася", age: 23 };
var masha = { name: "Маша", age: 18 };
var vovochka = { name: "Вовочка", age: 6 };
var people = [ vasya , masha , vovochka ];

function sort1(a, b){
    return a.age - b.age;
}
people.sort(sort1);
for (var i = 0; i < people.length; i++) {
    alert(people[i].name);
}

function printList(list) {

    var temp = list;
    var arr = [];
    while (temp) {
        arr.push(temp.value);
        temp = temp.next;
    }
    arr.reverse();
    for (var i = 0; i < arr.length; i++) {
        alert(arr[i]);
    }
}
var list = {
    value: 1,
    next: {
        value: 2,
        next: {
            value: 3,
            next: {
                value: 4,
                next: null
            }
        }
    }
};
printList(list);
function sort1(a, b){
    return a - b;
}
function aclean(arr) {

    var obj = {};
    for (var i = 0; i < arr.length; i++) {
        var str = arr[i].toLowerCase().split('').sort().join('');
        obj[str] = arr[i];
    }
    var res = [];
    for (var key in obj) {
        res.push(obj[key]);
    }
    return res;
}
var arr = ["воз", "киборг", "корсет", "ЗОВ", "гробик", "костер", "сектор"];
var b = arr.join('');
alert(aclean(arr));

function unique(arr) {

    var obj = {}, res = [];
    for (var i = 0; i < arr.length; i++) {
        var prom = arr[i];
        obj[prom] = arr[i];
    }
    for (var key in obj) {
        res.push(obj[key]);
    }
    return res;

    /*var res = [];
    input: for( var i = 0; i < arr.length; i++) {
        var str = arr[i];
        for (var k = 0; k < res.length; k++){
            if (res[k] == str) continue input;
        }
        res.push(str);
    }
    return res;

}
var strings = ["кришна", "кришна", "харе", "харе",
    "харе", "харе", "кришна", "кришна", "8-()"
];
alert(unique(strings));

var arr = ["Есть", "жизнь", "на", "Марсе"];
var arrLength = arr.map(function(arr){
    return arr.length;
});
alert(arrLength);

function getSum(arr) {

    var res = [];
    arr.reduce(function(sum, item) {
        res.push(sum + item);
        return sum + item;
    }, 0);
    return res;
}
arr = [ 1, 2, 3, 4, 5 ];
alert(getSum(arr));

function f(x) {

    if (arguments[0] !== undefined) {
        alert(1);
    } else {
        alert(0);
    }

}
f('');

function sum() {

    var arr = [], res = 0;
    for (var i = 0; i < arguments.length; i++) {
        arr[i] = arguments[i];
        res += arr[i];
    }
    return alert(res);
}
sum(2, 4);

function getDateAgo(date, days) {

    var day = new Date(date);
    day.setDate(day.getDate() - days);
    alert(day.getDate());

}
var date = new Date(2015, 0, 2);
getDateAgo(date, 5);

function getLastDayOfMonth(year, month) {

    var date = new Date(year, month + 1);
    date.setDate(date.getDate() - 1);
    return date.getDate();

}
alert(getLastDayOfMonth(2012, 1));

function getSecondsToday() {

    var date = new Date();
    return date.getHours() * 3600 + date.getMinutes() * 60 + date.getSeconds();
}
alert(getSecondsToday());

function getSecondsToTomorrow() {

    var date = new Date();
    var now = date.getHours() * 3600 + date.getMinutes() * 60 + date.getSeconds();
    return 24 * 3600 - now;

}
alert(getSecondsToTomorrow());

function formatDate(date) {

    var options = {
        year: '2-digit',
        //year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
    return date.toLocaleString('ru', options);
}
var date = new Date(2014, 0, 30);
alert(formatDate(date));*/

/*function formatDate(date) {

    var now = new Date() - date;
    if (now < 1000) return ('только что');
    var sec = Math.floor(now / 1000);
    if (sec < 60) return '30 сек. назад';
    var min = Math.floor(now / (1000 * 5 * 60));
    if (min < 5) return '5 мин. назад';

    /*var form = [];
    form = [
        '0' + date.getDate(),
        '0' + (date.getMonth() + 1),
        '' + date.getFullYear(),
        '0' + date.getHours(),
        '0' + date.getMinutes()
    ];
    for (var i = 0; i < form.length; i++) {
        form[i] = form[i].slice(-2);
    }
    return form.slice(0, 3).join('.') + ' ' + form.slice(3).join(':');*/
    /*var options = {
        year: '2-digit',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    };
    return date.toLocaleString('ru', options);

}
alert( formatDate(new Date(new Date - 86400 * 1000)) );

function sum(a) {

    return function(b) {
         alert(a + b);
    };

}
sum(1)(3);

function makeBuffer() {

    var str = '';
    function buffer(word) {
        if (arguments.length == 0) return str;
        str += word;
    }
    buffer.clear = function() {
        str = '';
    };
    return buffer;

}
var buffer = makeBuffer();
buffer(1);
buffer(1);
buffer(1);
buffer.clear();
alert(buffer());

function byField(field) {
    return function(a, b) {
        return a[field] > b[field] ? 1 : -1;
    }
}
var users = [{
    name: "Вася",
    surname: 'Иванов',
    age: 20
}, {
    name: "Петя",
    surname: 'Чапаев',
    age: 25
}, {
    name: "Маша",
    surname: 'Медведева',
    age: 18
}];
users.sort(byField('age'));
users.forEach(function(user) {
    alert( user.age );
});

function makeBuffer() {
    var str = '';
    function buffer(value) {
        if (arguments.length == 0) {
            return str;
        }
        str += value;
    }
    buffer.clear = function() {
        return str = '';
    };
    return buffer;
}

var buffer = makeBuffer();
buffer(2);
buffer(2);
buffer(2);
buffer('gfddfg');
buffer('ooklnfg');
buffer.clear();
alert(buffer());

function byField(field) {
    return function(a, b) {
        return a[field] > b[field] ? 1 : -1;
    }
}
var users = [{
    name: "Вася",
    surname: 'Иванов',
    age: 20
}, {
    name: "Петя",
    surname: 'Чапаев',
    age: 25
}, {
    name: "Маша",
    surname: 'Медведева',
    age: 18
}];

users.sort(byField('name'));
users.forEach(function(user) {
    alert( user.name );
}); // Вася, Маша, Петя

users.sort(byField('age'));
users.forEach(function(user) {
    alert( user.name );
}); // Маша, Вася, Петя

var calculator = {
    read: function() {
        this.a = +prompt('First num', 'num');
        this.b = +prompt('Second num', 'num');
    },
    sum: function() {
        return this.a + this.b;
    },
    mul: function() {
        return this.a * this.b;
    }
};

calculator.read();
alert( calculator.sum() );
alert( calculator.mul() );

var ladder = {
    step: 0,
    up: function() { // вверх по лестнице
        this.step++;
        return this;
    },
    down: function() { // вниз по лестнице
        this.step--;
        return this;
    },
    showStep: function() { // вывести текущую ступеньку
        alert( this.step );
    }

};
ladder.up().up().up().up().showStep();


function sum(a) {

    var rez = a;
    function prom(b) {
        rez += b;
        return prom;
    }
    prom.toString = function () {
        return rez;
    };
    return prom;
}
alert(sum(5)(3)(10));

function Calculator() {

    var a = '', b = '';
    this.read = function() {
        a = +prompt('Введите число', 'Первое число');
        b = +prompt('Введите число', 'Второе число');
    };

    this.sum = function() {
        return a + b;
    };

    this.mul = function() {
        return a * b;
    };
}
var calculator = new Calculator();
calculator.read();

alert( "Сумма = " + calculator.sum() );
     alert( "Произведение = " + calculator.mul() );

function Accumulator(a) {

    this.value = a;
    this.read = function() {
        this.num = +prompt('Число', 0);
        this.value += this.num;
    };

}
var accumulator = new Accumulator(1); // начальное значение 1
accumulator.read(); // прибавит ввод prompt к текущему значению
accumulator.read(); // прибавит ввод prompt к текущему значению
     alert( accumulator.value ); // выведет текущее значение

function Calculator() {

     var methods = {
     '+': function(a, b) {
     return a + b;
     },
     '-': function(a, b) {
     return a - b;
        }
    };

     this.calculate = function (str) {
     var s = str.split(' '),
     s0 = s[0],
     s1 = s[1],
     s2 = s[2];

     if (!methods[s1] || isNaN(s0) || isNaN(s2)) {
     return NaN;
     }
     return methods[s1](+s0, +s2);
     };

     this.addMethod = function(name, func) {
     methods[name] = func;
     };

}
var calc = new Calculator;

     calc.addMethod("*", function(a, b) {
     return a * b;
     });
     calc.addMethod("/", function(a, b) {
     return a / b;
     });
     calc.addMethod("**", function(a, b) {
     return Math.pow(a, b);
     });

     alert(calc.calculate("2 ** 3"));*/






