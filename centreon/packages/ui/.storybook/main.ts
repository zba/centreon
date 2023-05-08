import type { StorybookConfig } from "@storybook/react-vite";
import remarkGfm from "remark-gfm";

const config: StorybookConfig = {
  stories: ["../src/**/*.mdx", "../src/**/*.stories.@(js|jsx|ts|tsx)"],
  addons: [
    "@storybook/addon-essentials",
    {
      name: "@storybook/addon-docs",
      options: {
        configureJSX: true,
        // FIXME jest issue : Cannot use import statement outside a module
        // mdxPluginOptions: {
        //   mdxCompileOptions: {
        //     remarkPlugins: [remarkGfm],
        //   },
        // },
      },
    },
    "@storybook/addon-styling",
    "@storybook/addon-a11y",
    "@storybook/addon-interactions",
    "storybook-addon-mock",
    "storybook-dark-mode",
  ],
  features: {},
  framework: {
    name: "@storybook/react-vite",
    options: {},
  },
  typescript: {
    reactDocgen: "react-docgen-typescript",
  },
  docs: {
    autodocs: "tag",
  },
};

export default config;
