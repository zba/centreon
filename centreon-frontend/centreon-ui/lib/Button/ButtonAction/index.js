"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

var _IconAction = require("../../Icon/IconAction");

var _IconAction2 = _interopRequireDefault(_IconAction);

require("./button-action.scss");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var ButtonAction = function ButtonAction(_ref) {
  var buttonActionType = _ref.buttonActionType,
      buttonIconType = _ref.buttonIconType,
      onClick = _ref.onClick,
      iconColor = _ref.iconColor,
      title = _ref.title;
  return _react2.default.createElement(
    "span",
    {
      className: "button-action button-action-" + buttonActionType + " " + iconColor,
      onClick: onClick
    },
    _react2.default.createElement(_IconAction2.default, { iconColor: iconColor ? iconColor : '', iconActionType: buttonIconType }),
    title && _react2.default.createElement(
      "span",
      { className: "button-action-title" },
      title
    )
  );
};

exports.default = ButtonAction;