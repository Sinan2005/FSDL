import { useState, useCallback } from "react";
import "./App.css";

function Calculator() {
  const [display, setDisplay] = useState("0");
  const [prev, setPrev] = useState(null);
  const [op, setOp] = useState(null);
  const [resetNext, setResetNext] = useState(false);
  const [expr, setExpr] = useState("");

  const inputDigit = useCallback(
    (d) => {
      setDisplay((cur) => {
        if (resetNext) {
          setResetNext(false);
          return String(d);
        }
        if (cur === "0") return String(d);
        if (cur.length >= 12) return cur;
        return cur + d;
      });
    },
    [resetNext]
  );

  const inputDot = useCallback(() => {
    setDisplay((cur) => {
      if (resetNext) {
        setResetNext(false);
        return "0.";
      }
      if (cur.includes(".")) return cur;
      return cur + ".";
    });
  }, [resetNext]);

  const calculate = (a, b, operator) => {
    switch (operator) {
      case "+": return a + b;
      case "−": return a - b;
      case "×": return a * b;
      case "÷": return b === 0 ? "Error" : a / b;
      case "%": return a % b;
      default:  return b;
    }
  };

  const inputOp = useCallback(
    (nextOp) => {
      const cur = parseFloat(display);
      if (prev !== null && !resetNext) {
        const result = calculate(prev, cur, op);
        const rounded = parseFloat(result.toFixed(10));
        setDisplay(String(rounded));
        setPrev(rounded);
        setExpr(String(rounded) + " " + nextOp);
      } else {
        setPrev(cur);
        setExpr(display + " " + nextOp);
      }
      setOp(nextOp);
      setResetNext(true);
    },
    [display, prev, op, resetNext]
  );

  const equals = useCallback(() => {
    if (op === null || prev === null) return;
    const cur = parseFloat(display);
    const result = calculate(prev, cur, op);
    const rounded =
      result === "Error" ? "Error" : parseFloat(result.toFixed(10));
    setExpr(expr + " " + display + " =");
    setDisplay(String(rounded));
    setPrev(null);
    setOp(null);
    setResetNext(true);
  }, [display, prev, op, expr]);

  const clear      = () => { setDisplay("0"); setPrev(null); setOp(null); setResetNext(false); setExpr(""); };
  const toggleSign = () => setDisplay((d) => (d === "0" ? "0" : d.startsWith("-") ? d.slice(1) : "-" + d));
  const percent    = () => setDisplay((d) => String(parseFloat(d) / 100));
  const backspace  = () => setDisplay((d) => (d.length > 1 ? d.slice(0, -1) : "0"));

  const buttons = [
    { label: "AC",  action: clear,            type: "func" },
    { label: "+/−", action: toggleSign,        type: "func" },
    { label: "%",   action: percent,           type: "func" },
    { label: "÷",   action: () => inputOp("÷"), type: "op"   },
    { label: "7",   action: () => inputDigit(7), type: "num" },
    { label: "8",   action: () => inputDigit(8), type: "num" },
    { label: "9",   action: () => inputDigit(9), type: "num" },
    { label: "×",   action: () => inputOp("×"), type: "op"   },
    { label: "4",   action: () => inputDigit(4), type: "num" },
    { label: "5",   action: () => inputDigit(5), type: "num" },
    { label: "6",   action: () => inputDigit(6), type: "num" },
    { label: "−",   action: () => inputOp("−"), type: "op"   },
    { label: "1",   action: () => inputDigit(1), type: "num" },
    { label: "2",   action: () => inputDigit(2), type: "num" },
    { label: "3",   action: () => inputDigit(3), type: "num" },
    { label: "+",   action: () => inputOp("+"), type: "op"   },
    { label: "⌫",   action: backspace,          type: "num"  },
    { label: "0",   action: () => inputDigit(0), type: "num" },
    { label: ".",   action: inputDot,           type: "num"  },
    { label: "=",   action: equals,             type: "eq"   },
  ];

  const displayLen = display.replace("-", "").replace(".", "").length;
  const fontSize =
    displayLen > 10 ? "1.6rem" : displayLen > 7 ? "2rem" : "2.6rem";

  return (
    <div className="calc-wrapper">
      <div className="calculator">
        {/* Display */}
        <div className="display">
          <div className="expression">{expr || "\u00A0"}</div>
          <div className="number" style={{ fontSize }}>
            {display}
          </div>
        </div>

        {/* Buttons */}
        <div className="buttons">
          {buttons.map((btn, i) => (
            <button
              key={i}
              className={`btn btn-${btn.type} ${btn.label === op ? "active-op" : ""}`}
              onClick={btn.action}
            >
              {btn.label}
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}

export default Calculator;