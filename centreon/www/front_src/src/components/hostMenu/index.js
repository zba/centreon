import React, { Component } from "react";
import numeral from "numeral";

class HostMenu extends Component {
  state = {
    toggled: false
  };

  toggle = () => {
    const { toggled } = this.state;
    this.setState({
      toggled: !toggled
    });
  };

  render() {
    const { data } = this.props;

    if (!data || !data.total) {
      return null;
    }

    const { down, warning, unreachable, ok, pending, total, url } = data;

    const { toggled } = this.state;

    return (
      <div class={"wrap-right-hosts" + (toggled ? " submenu-active" : "")}>
        <span class="wrap-right-icon" onClick={this.toggle.bind(this)}>
          <span class="iconmoon icon-hosts">
            {pending > 0 ? <span class="custom-icon" /> : null}
          </span>
          <span class="wrap-right-icon__name">Hosts</span>
        </span>

        <span class={"wrap-middle-icon round round-small "+ (down.unhandled > 0 ? "red" : "red-bordered")}>
          <a class="number">
            <span>{numeral(down.unhandled).format("0a")}</span>
          </a>
        </span>
        <span class={"wrap-middle-icon round round-small "+ (unreachable.unhandled > 0 ? "gray-dark" : "gray-dark-bordered")}>
          <a class="number">
            <span>{numeral(unreachable.unhandled).format("0a")}</span>
          </a>
        </span>
        <span class={"wrap-middle-icon round round-big "+ (ok.total > 0 ? "green" : "green-bordered")}>
          <a class="number">
            <span>{numeral(ok.total).format("0a")}</span>
          </a>
        </span>

        <span class="toggle-submenu-arrow" onClick={this.toggle.bind(this)} />
        <div class="submenu host">
          <div class="submenu-inner">
            <ul class="submenu-items list-unstyled">
              <li class="submenu-item">
                <a
                  href={"./main.php?p=20202&o=h&search="}
                  class="submenu-item-link"
                >
                  <span>All hosts</span>
                  <span class="submenu-count">{total}</span>
                </a>
              </li>
              <li class="submenu-item">
                <a
                  href={"./main.php?p=20202&o=h_down&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored red">Down hosts</span>
                  <span class="submenu-count">
                    {down.unhandled}/{down.total}
                  </span>
                </a>
              </li>
              <li class="submenu-item">
                <a
                  href={"./main.php?p=20202&o=h_unreachable&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored gray">Unreachable hosts</span>
                  <span class="submenu-count">
                    {unreachable.unhandled}/{unreachable.total}
                  </span>
                </a>
              </li>
              <li class="submenu-item">
                <a
                  href={"./main.php?p=20202&o=h_up&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored green">Ok hosts</span>
                  <span class="submenu-count">{ok}</span>
                </a>
              </li>
              <li class="submenu-item">
                <a
                  href={"./main.php?p=20202&o=h_pending&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored blue">{pending} Pending hosts</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    );
  }
}

export default HostMenu;
